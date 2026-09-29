<?php

use Carbon\CarbonImmutable;
use Filament\Facades\Filament;
use Huvant\Calendar\Support\Calendar;
use Huvant\Home\Filament\Pages\HomePage;
use Huvant\Home\Support\Home;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    foreach (['huvant-worklog', 'huvant-calendar', 'huvant-home'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    config(['huvant-bridge.webhook.url' => 'http://minutes.test/api/v1/erp/webhook', 'huvant-bridge.webhook.secret' => 'brief-secret']);
});

function homeUser(): User
{
    return User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true, 'name' => 'Giulia Rossi']));
}

function homeTask(User $user, string $title, ?string $deadline, string $state = 'in_progress'): int
{
    $project = Project::withoutEvents(fn (): Project => Project::factory()->create());
    $id = DB::table('projects_tasks')->insertGetId(['title' => $title, 'state' => $state, 'project_id' => $project->getKey(), 'deadline' => $deadline, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    DB::table('projects_task_users')->insert(['task_id' => $id, 'user_id' => $user->getKey()]);

    return $id;
}

it('groups one\'s open tasks by urgency', function () {
    $user = homeUser();
    $today = CarbonImmutable::parse('2026-10-05');
    homeTask($user, 'Late', '2026-10-01 18:00:00');
    homeTask($user, 'Tomorrow', '2026-10-06 18:00:00');
    homeTask($user, 'Later', '2026-10-20 18:00:00');
    homeTask($user, 'Open-ended', null);
    homeTask($user, 'Finished', '2026-10-01 18:00:00', 'done');
    $this->actingAs($user);

    $buckets = collect(Home::myTasks($user, $today))->map(fn ($rows) => collect($rows)->map(fn ($r) => $r['task']->title)->all())->all();
    expect($buckets)->toBe(['overdue' => ['Late'], 'soon' => ['Tomorrow'], 'later' => ['Later'], 'none' => ['Open-ended']])
        ->and(Home::dueLabel(-4))->toBe('4 days late')
        ->and(Home::dueLabel(0))->toBe('Due today')
        ->and(Home::dueLabel(1))->toBe('Due tomorrow');
});

it('asks Milo with a signed request and keeps the brief', function () {
    $user = homeUser();
    homeTask($user, 'Offerta Atlas', now()->subDays(2)->toDateTimeString());
    $this->actingAs($user);
    Http::fake(['minutes.test/*' => Http::response([
        'headline' => 'One overdue task', 'summary' => 'Start from Atlas.', 'focus' => [['title' => 'Offerta Atlas', 'why' => 'Two days late.']], 'heads_up' => ['Review at 10:00'],
    ])]);

    $brief = Home::generate($user, 'morning');

    Http::assertSent(function (Request $request) use ($user): bool {
        $expected = 'sha256='.hash_hmac('sha256', $request->header('X-Huvant-Timestamp')[0].'.'.$request->body(), 'brief-secret');

        return $request->url() === 'http://minutes.test/api/v1/erp/milo-briefing'
            && $request->header('X-Huvant-Signature')[0] === $expected
            && $request['email'] === $user->email && $request['tasks'][0]['title'] === 'Offerta Atlas';
    });
    expect($brief->headline)->toBe('One overdue task')->and($brief->focus[0]['why'])->toBe('Two days late.');

    Http::fake(['minutes.test/*' => Http::response(['detail' => 'brief_failed'], 502)]);
    $again = Home::generate($user, 'midday');
    expect($again->headline)->toBe('One overdue task'); // a failed attempt does not hide the last good brief
});

it('renders the home page and schedules the briefs at 8 and 13 on weekdays', function () {
    $user = homeUser();
    homeTask($user, 'Montare il banco', now()->addDay()->toDateTimeString());
    $this->actingAs($user);

    Livewire::test(HomePage::class)->assertOk()->assertSee('My tasks')->assertSee('Montare il banco')->assertSee('Due tomorrow')->assertSee('8:00 and 13:00');

    $events = collect(app(Schedule::class)->events())->filter(fn ($e) => str_contains($e->command, 'huvant:milo-briefings'));
    expect($events->map(fn ($e) => $e->expression)->values()->all())->toBe(['0 8 * * 1-5', '0 13 * * 1-5']);
});

it('shows what is waiting, today\'s time and the meetings from the Minutes', function () {
    $user = homeUser();
    $boss = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true, 'name' => 'Boss']));
    $task = homeTask($user, 'Appena assegnato', null);
    DB::table('projects_task_users')->where('task_id', $task)->delete();
    \Webkul\Project\Models\Task::query()->findOrFail($task)->users()->attach($user->getKey()); // a real assignment, dated
    Calendar::save($boss, ['kind' => 'meeting', 'title' => 'Kick-off', 'date' => now()->addDay()->toDateString(), 'from' => '10:00', 'to' => '11:00', 'attendees' => [$user->id]]);
    Worklog::addEntry($user, $task, now()->toDateString(), 1.5, 'Studio del capitolato');
    Http::fake(['minutes.test/api/v1/erp/my-meetings' => Http::response(['meetings' => [
        ['title'    => 'Planning Atlas', 'date' => '2026-09-28', 'system' => 'minutes', 'url' => '/riunioni/?meeting=x', 'mine' => 1,
            'items' => [['kind' => 'action', 'text' => 'Preparare l\'offerta', 'owner' => 'Giulia Rossi', 'due' => null, 'mine' => true]]],
    ]])]);
    $this->actingAs($user);

    $waiting = Home::waiting($user);
    expect($waiting['invites']->pluck('title')->all())->toBe(['Kick-off'])
        ->and($waiting['assigned']->pluck('title')->all())->toBe(['Appena assegnato'])
        ->and(Home::today($user)['hours'])->toBe(1.5);

    $event = $waiting['invites']->first();
    Livewire::test(HomePage::class)->assertOk()
        ->assertSee('Waiting on you')->assertSee('Kick-off')->assertSee('Studio del capitolato')->assertSee('Planning Atlas')->assertSee('Preparare')
        ->assertActionExists('logTime')
        ->call('respondInvite', $event->id, 'accepted');
    expect(Calendar::pendingFor($user))->toHaveCount(0);
});
