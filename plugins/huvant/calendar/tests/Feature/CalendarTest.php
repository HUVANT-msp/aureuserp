<?php

use Carbon\CarbonImmutable;
use Filament\Facades\Filament;
use Huvant\Calendar\Filament\Pages\CalendarPage;
use Huvant\Calendar\Models\Event;
use Huvant\Calendar\Support\Calendar;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-calendar');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function calUser(string $name): User
{
    return User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true, 'name' => $name]));
}

function calAdmin(): User
{
    return User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(calUser('Admin Huvant'))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
}

it('creates a meeting, invites people and notifies them', function () {
    [$anna, $bruno] = [calUser('Anna'), calUser('Bruno')];
    $this->actingAs($anna);

    $event = Calendar::save($anna, ['kind' => 'meeting', 'title' => 'Kick-off', 'date' => '2026-10-05', 'from' => '10:00', 'to' => '11:00', 'attendees' => [$bruno->id]]);

    expect($event->attendees()->pluck('response', 'user_id')->all())->toBe([$anna->id => 'accepted', $bruno->id => 'pending'])
        ->and(DB::table('notifications')->where('notifiable_id', $bruno->id)->count())->toBe(1)
        ->and(Calendar::pendingFor($bruno)->pluck('id')->all())->toBe([$event->id]);

    Calendar::respond($bruno, $event, 'accepted');
    expect($event->attendees()->where('user_id', $bruno->id)->value('response'))->toBe('accepted')
        ->and(DB::table('notifications')->where('notifiable_id', $anna->id)->count())->toBe(1);

    Calendar::save($anna, ['kind' => 'meeting', 'title' => 'Kick-off', 'date' => '2026-10-05', 'from' => '14:00', 'to' => '15:00', 'attendees' => [$bruno->id]], $event);
    expect($event->attendees()->where('user_id', $bruno->id)->value('response'))->toBe('pending');

    expect(fn () => Calendar::save($bruno, ['kind' => 'meeting', 'title' => 'X', 'date' => '2026-10-05', 'from' => '9:00', 'to' => '9:30'], $event))->toThrow(RuntimeException::class)
        ->and(fn () => Calendar::save($anna, ['kind' => 'meeting', 'title' => 'X', 'date' => '2026-10-05', 'from' => '11:00', 'to' => '10:00']))->toThrow(RuntimeException::class);
});

it('reports conflicts and says where people are and whether they can be reached', function () {
    [$anna, $bruno] = [calUser('Anna'), calUser('Bruno')];
    $monday = CarbonImmutable::parse('2026-10-05');
    Calendar::save($anna, ['kind' => 'remote', 'date' => '2026-10-05', 'all_day' => true]);
    Calendar::save($bruno, ['kind' => 'meeting', 'title' => 'Review', 'date' => '2026-10-05', 'from' => '10:00', 'to' => '11:30']);
    Calendar::save($bruno, ['kind' => 'away', 'date' => '2026-10-07', 'all_day' => true]);

    expect(Calendar::conflicts([$bruno->id], $monday->setTime(11, 0), $monday->setTime(12, 0)))->toBe(['Bruno: Meeting “Review” 10:00–11:30'])
        ->and(Calendar::conflicts([$anna->id], $monday->setTime(11, 0), $monday->setTime(12, 0)))->toBe(['Anna: Remote “Remote” (all day)']);

    $board = Calendar::board(collect([$anna, $bruno]), $monday, 5, $monday->setTime(10, 30));
    [$annaRow, $brunoRow] = $board['rows'];
    expect($annaRow['cells'][0]['presence'])->toBe('remote')
        ->and($annaRow['now'][0])->toBe('Available · remote')
        ->and($brunoRow['cells'][0]['meetings'])->toBe(1)
        ->and($brunoRow['now'])->toBe(['In a meeting', 'busy', 'until 11:30'])
        ->and($brunoRow['cells'][2]['presence'])->toBe('away');
});

it('shows only "Busy" for somebody else\'s private event', function () {
    [$anna, $bruno] = [calUser('Anna'), calUser('Bruno')];
    $event = Calendar::save($anna, ['kind' => 'other', 'title' => 'Medico', 'date' => '2026-10-05', 'from' => '09:00', 'to' => '10:00', 'private' => true]);

    expect(Calendar::view($event->load('attendees'), $bruno)['title'])->toBe('Busy')
        ->and(Calendar::view($event, $anna)['title'])->toBe('Medico');
});

it('renders both views and the event dialog', function () {
    $admin = calAdmin();
    $bruno = calUser('Bruno');
    $this->actingAs($admin);
    $event = Calendar::save($admin, ['kind' => 'meeting', 'title' => 'Planning', 'date' => CarbonImmutable::today()->toDateString(), 'from' => '10:00', 'to' => '11:00', 'attendees' => [$bruno->id]]);

    Livewire::test(CalendarPage::class)->assertOk()->assertSee("Who's where", false)
        ->call('setTab', 'agenda')->assertSee('Planning')
        ->call('openEvent', $event->id)->assertSee('Bruno')->assertSee('Awaiting reply')
        ->call('markToday', 'remote')->assertOk()
        ->call('setTab', 'presence')->call('setMyDay', CarbonImmutable::today()->subDays(3)->toDateString(), 'away')->assertOk();

    expect(Event::query()->where('kind', 'remote')->where('organizer_id', $admin->id)->exists())->toBeTrue();
    $this->get(CalendarPage::getUrl())->assertOk();
});

it('sets one\'s presence on any day, splitting longer events around it', function () {
    $anna = calUser('Anna');
    $trip = Calendar::save($anna, ['kind' => 'travel', 'date' => '2026-10-05', 'date_to' => '2026-10-07', 'all_day' => true]);

    Calendar::setPresence($anna, CarbonImmutable::parse('2026-10-06'), 'remote');
    $mine = Event::query()->orderBy('starts_at')->get()->map(fn ($e) => $e->kind.' '.$e->starts_at->format('d').'-'.$e->ends_at->format('d'))->all();
    expect($mine)->toBe(['travel 05-06', 'remote 06-07', 'travel 07-08'])
        ->and(Event::query()->whereKey($trip->id)->exists())->toBeFalse();

    Calendar::setPresence($anna, CarbonImmutable::parse('2026-10-06'), 'office');
    expect(Event::query()->where('kind', 'remote')->exists())->toBeFalse()
        ->and(Event::query()->count())->toBe(2);

    $board = Calendar::board(collect([$anna]), CarbonImmutable::parse('2026-10-05'), 3, CarbonImmutable::parse('2026-10-05 10:00'));
    expect(array_column($board['rows'][0]['cells'], 'presence'))->toBe(['travel', 'office', 'travel']);
});
