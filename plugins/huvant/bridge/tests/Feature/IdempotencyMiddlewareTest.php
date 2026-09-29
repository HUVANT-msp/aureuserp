<?php

use Huvant\Bridge\Http\Middleware\EnsureActiveUser;
use Huvant\Bridge\Http\Middleware\EnsureBridgeCompanyBoundary;
use Huvant\Bridge\Http\Middleware\EnsureIdempotentBridgeRequest;
use Huvant\Bridge\Http\Middleware\TouchTaskAfterPivotUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-bridge');
    $user = SecurityHelper::authenticateWithPermissions([]);
    $user->forceFill(['is_active' => true])->saveQuietly();

    if (! Route::has('huvant.bridge.test.idempotency')) {
        Route::post('/_huvant-bridge/idempotency-test', function (Request $request) {
            DB::table('password_reset_tokens')->insert([
                'email'      => $request->string('email')->toString(),
                'token'      => 'test-token',
                'created_at' => now(),
            ]);

            return response()->json(['created' => true], 201);
        })->middleware(EnsureIdempotentBridgeRequest::class)->name('huvant.bridge.test.idempotency');

        Route::post('/_huvant-bridge/idempotency-upload-test', fn () => response()->json(['created' => true], 201))
            ->middleware(EnsureIdempotentBridgeRequest::class)
            ->name('huvant.bridge.test.idempotency-upload');
    }
});

afterEach(function () {
    Carbon::setTestNow();
});

it('hashes multipart requests by fields and file content rather than MIME boundaries', function () {
    $headers = ['Idempotency-Key' => 'meeting-upload-1'];

    $this->post('/_huvant-bridge/idempotency-upload-test', [
        'label'    => 'minutes',
        'document' => UploadedFile::fake()->createWithContent('minutes.pdf', 'same-content'),
    ], $headers)->assertCreated();
    $this->post('/_huvant-bridge/idempotency-upload-test', [
        'document' => UploadedFile::fake()->createWithContent('minutes.pdf', 'same-content'),
        'label'    => 'minutes',
    ], $headers)->assertCreated()->assertHeader('Idempotency-Replayed', 'true');
});

it('replays a completed request without repeating its mutation', function () {
    $headers = ['Idempotency-Key' => 'meeting-user-1'];
    $payload = ['email' => 'new-user@huvant.com'];

    $this->postJson('/_huvant-bridge/idempotency-test', $payload, $headers)->assertCreated();
    $this->postJson('/_huvant-bridge/idempotency-test', $payload, $headers)
        ->assertCreated()
        ->assertHeader('Idempotency-Replayed', 'true');

    expect(DB::table('password_reset_tokens')->where('email', $payload['email'])->count())->toBe(1);
});

it('rejects reuse of an idempotency key with a different payload', function () {
    $headers = ['Idempotency-Key' => 'meeting-user-2'];

    $this->postJson('/_huvant-bridge/idempotency-test', ['email' => 'first@huvant.com'], $headers)->assertCreated();
    $this->postJson('/_huvant-bridge/idempotency-test', ['email' => 'second@huvant.com'], $headers)
        ->assertConflict();
});

it('protects core task project and partner mutations when an idempotency key is supplied', function (string $routeName) {
    expect(Route::getRoutes()->getByName($routeName)->gatherMiddleware())
        ->toContain(EnsureActiveUser::class)
        ->toContain(EnsureBridgeCompanyBoundary::class)
        ->toContain(EnsureIdempotentBridgeRequest::class);
})->with([
    'admin.api.v1.projects.tasks.store',
    'admin.api.v1.projects.projects.store',
    'admin.api.v1.projects.task-stages.store',
    'admin.api.v1.partners.partners.store',
]);

it('touches successful task pivot updates inside the idempotent mutation flow', function () {
    $middleware = Route::getRoutes()->getByName('admin.api.v1.projects.tasks.update')->gatherMiddleware();

    expect($middleware)
        ->toContain(EnsureIdempotentBridgeRequest::class)
        ->toContain(TouchTaskAfterPivotUpdate::class)
        ->and(array_search(EnsureIdempotentBridgeRequest::class, $middleware, true))
        ->toBeLessThan(array_search(TouchTaskAfterPivotUpdate::class, $middleware, true));
});

it('advances a pivot-only task update beyond the original database second and emits updated', function () {
    Carbon::setTestNow('2026-09-29 12:00:00 UTC');
    $companyId = auth()->user()->default_company_id;
    $project = Project::withoutEvents(fn (): Project => Project::query()->create([
        'name'       => 'Pivot regression project',
        'visibility' => 'internal',
        'company_id' => $companyId,
    ]));
    $stage = TaskStage::withoutEvents(fn (): TaskStage => TaskStage::query()->create([
        'name'       => 'Pivot regression stage',
        'project_id' => $project->id,
        'company_id' => $companyId,
    ]));
    $task = Task::withoutEvents(fn (): Task => Task::query()->create([
        'title'      => 'Pivot regression task',
        'state'      => 'in_progress',
        'project_id' => $project->id,
        'stage_id'   => $stage->id,
        'company_id' => $companyId,
    ]));
    $originalUpdatedAt = $task->updated_at->copy()->startOfSecond();
    $updatedEvents = 0;

    Task::updated(function (Task $updatedTask) use ($task, &$updatedEvents): void {
        if ($updatedTask->is($task)) {
            $updatedEvents++;
        }
    });

    $request = Request::create("/admin/api/v1/projects/tasks/{$task->id}", 'PATCH', [
        'users' => [],
    ]);
    $route = new IlluminateRoute(
        ['PATCH'],
        'admin/api/v1/projects/tasks/{task}',
        fn () => null,
    );
    $route->name('admin.api.v1.projects.tasks.update');
    $route->bind($request);
    $request->setRouteResolver(fn () => $route);

    $response = app(TouchTaskAfterPivotUpdate::class)->handle(
        $request,
        fn () => response()->json(['updated' => true]),
    );

    expect($response->getStatusCode())->toBe(200)
        ->and($task->refresh()->updated_at->greaterThan($originalUpdatedAt))->toBeTrue()
        ->and($task->updated_at->equalTo($originalUpdatedAt->copy()->addSecond()))->toBeTrue()
        ->and($updatedEvents)->toBe(1);
});

it('rejects an inactive user before a core mutation reaches its controller', function () {
    auth()->user()->forceFill(['is_active' => false])->saveQuietly();

    $this->postJson(route('admin.api.v1.projects.tasks.store'), [])
        ->assertForbidden()
        ->assertJsonPath('message', 'This account is inactive.');
});
