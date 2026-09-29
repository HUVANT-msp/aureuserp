<?php

use Huvant\Bridge\Http\Middleware\EnsureBridgeCompanyBoundary;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as IlluminateRoute;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-bridge');
});

function activeCompanyBoundaryUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);
    $user->forceFill(['is_active' => true])->saveQuietly();

    return $user;
}

it('creates a project in the actor default company exactly once', function () {
    $actor = activeCompanyBoundaryUser(['create_project_project']);
    $actor->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();
    $stage = ProjectStage::factory()->create(['company_id' => $actor->default_company_id]);
    $payload = [
        'name'       => 'Bridge idempotent project',
        'visibility' => 'internal',
        'stage_id'   => $stage->id,
    ];
    $headers = ['Idempotency-Key' => 'project-default-company-1'];

    $this->postJson(route('admin.api.v1.projects.projects.store'), $payload, $headers)
        ->assertCreated()
        ->assertJsonPath('data.company_id', $actor->default_company_id);
    $this->postJson(route('admin.api.v1.projects.projects.store'), $payload, $headers)
        ->assertCreated()
        ->assertHeader('Idempotency-Replayed', 'true');

    expect(Project::withoutGlobalScopes()->where('name', $payload['name'])->count())->toBe(1)
        ->and(Project::withoutGlobalScopes()->where('name', $payload['name'])->value('company_id'))
        ->toBe($actor->default_company_id);
});

it('accepts a project stage shared by every company', function () {
    $actor = activeCompanyBoundaryUser(['create_project_project']);
    $actor->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();
    $sharedStage = ProjectStage::factory()->create(['company_id' => null]);

    $this->postJson(route('admin.api.v1.projects.projects.store'), [
        'name'       => 'Project on a shared stage',
        'visibility' => 'internal',
        'stage_id'   => $sharedStage->id,
    ], ['Idempotency-Key' => 'project-shared-stage-1'])
        ->assertCreated()
        ->assertJsonPath('data.company_id', $actor->default_company_id);
});

it('still rejects a project stage owned by another company', function () {
    $actor = activeCompanyBoundaryUser(['create_project_project']);
    $actor->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();
    $foreignStage = ProjectStage::factory()->create(['company_id' => Company::factory()->create()->id]);

    $this->postJson(route('admin.api.v1.projects.projects.store'), [
        'name'       => 'Project on a foreign stage',
        'visibility' => 'internal',
        'stage_id'   => $foreignStage->id,
    ], ['Idempotency-Key' => 'project-foreign-stage-1'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('stage_id');
});

function companyBoundaryRequest(
    User $user,
    string $method,
    string $name,
    string $routeUri,
    string $requestUri,
    array $input = [],
): Request {
    $request = Request::create($requestUri, $method, $input);
    $route = new IlluminateRoute([$method], $routeUri, fn () => null);
    $route->name($name);
    $route->bind($request);
    $request->setRouteResolver(fn () => $route);
    $request->setUserResolver(fn () => $user);

    return $request;
}

it('injects the actor default company into project and partner creates', function (string $routeName, string $uri) {
    $actor = activeCompanyBoundaryUser();
    $request = companyBoundaryRequest($actor, 'POST', $routeName, $uri, '/'.$uri);

    $response = app(EnsureBridgeCompanyBoundary::class)->handle(
        $request,
        fn (Request $request) => response()->json($request->all()),
    );

    expect($response->getStatusCode())->toBe(200)
        ->and($request->integer('company_id'))->toBe((int) $actor->default_company_id);
})->with([
    ['admin.api.v1.projects.projects.store', 'admin/api/v1/projects/projects'],
    ['admin.api.v1.partners.partners.store', 'admin/api/v1/partners/partners'],
]);

it('denies a non-super-admin updating a project in another company', function () {
    $actor = activeCompanyBoundaryUser();
    $otherCompany = Company::factory()->create();
    $actor->allowedCompanies()->detach($otherCompany->id);
    $project = Project::factory()->create(['company_id' => $otherCompany->id]);
    $request = companyBoundaryRequest(
        $actor,
        'PUT',
        'admin.api.v1.projects.projects.update',
        'admin/api/v1/projects/projects/{project}',
        "/admin/api/v1/projects/projects/{$project->id}",
        ['name' => 'Forbidden update'],
    );
    $request->route()->setParameter('project', $project);

    $response = app(EnsureBridgeCompanyBoundary::class)->handle($request, fn () => response()->noContent());

    expect($response->getStatusCode())->toBe(403);
});

it('rejects moving an existing project to another allowed company', function () {
    $actor = activeCompanyBoundaryUser();
    $otherCompany = Company::factory()->create();
    $actor->allowedCompanies()->syncWithoutDetaching([$otherCompany->id]);
    $project = Project::factory()->create(['company_id' => $actor->default_company_id]);
    $request = companyBoundaryRequest(
        $actor,
        'PATCH',
        'admin.api.v1.projects.projects.update',
        'admin/api/v1/projects/projects/{project}',
        "/admin/api/v1/projects/projects/{$project->id}",
        ['company_id' => $otherCompany->id],
    );
    $request->route()->setParameter('project', $project);

    $response = app(EnsureBridgeCompanyBoundary::class)->handle($request, fn () => response()->noContent());

    expect($response->getStatusCode())->toBe(422)
        ->and(json_decode($response->getContent(), true)['errors'])->toHaveKey('company_id')
        ->and($project->fresh()->company_id)->toBe($actor->default_company_id);
});

it('forbids moving an existing project to an unavailable company', function () {
    $actor = activeCompanyBoundaryUser();
    $otherCompany = Company::factory()->create();
    $actor->allowedCompanies()->detach($otherCompany->id);
    $project = Project::factory()->create(['company_id' => $actor->default_company_id]);
    $request = companyBoundaryRequest(
        $actor,
        'PATCH',
        'admin.api.v1.projects.projects.update',
        'admin/api/v1/projects/projects/{project}',
        "/admin/api/v1/projects/projects/{$project->id}",
        ['company_id' => $otherCompany->id],
    );
    $request->route()->setParameter('project', $project);

    $response = app(EnsureBridgeCompanyBoundary::class)->handle($request, fn () => response()->noContent());

    expect($response->getStatusCode())->toBe(403)
        ->and($project->fresh()->company_id)->toBe($actor->default_company_id);
});

it('rejects a task stage that belongs to a different project', function () {
    $actor = activeCompanyBoundaryUser();
    $companyId = $actor->default_company_id;
    $project = Project::factory()->create(['company_id' => $companyId]);
    $otherProject = Project::factory()->create(['company_id' => $companyId]);
    $stage = TaskStage::factory()->create([
        'project_id' => $otherProject->id,
        'company_id' => $companyId,
    ]);
    $request = companyBoundaryRequest(
        $actor,
        'POST',
        'admin.api.v1.projects.tasks.store',
        'admin/api/v1/projects/tasks',
        '/admin/api/v1/projects/tasks',
        [
            'project_id' => $project->id,
            'stage_id'   => $stage->id,
        ],
    );

    $response = app(EnsureBridgeCompanyBoundary::class)->handle($request, fn () => response()->noContent());

    expect($response->getStatusCode())->toBe(422)
        ->and(json_decode($response->getContent(), true)['errors'])->toHaveKey('stage_id');
});

it('derives a task stage company from its project', function () {
    $actor = activeCompanyBoundaryUser();
    $project = Project::factory()->create(['company_id' => $actor->default_company_id]);

    $stage = TaskStage::factory()->create([
        'project_id' => $project->id,
        'company_id' => null,
    ]);

    expect($stage->company_id)->toBe($project->company_id);
});
