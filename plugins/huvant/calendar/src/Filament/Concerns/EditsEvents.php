<?php

namespace Huvant\Calendar\Filament\Concerns;

use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Huvant\Calendar\Models\Event;
use Huvant\Calendar\Support\Calendar;
use Illuminate\Support\HtmlString;
use RuntimeException;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

/** The event form (new or edit) with live conflicts, for any page that offers it. */
trait EditsEvents
{
    abstract protected function calendarUser(): User;

    /** The event the "edit" form works on, if any. */
    protected function editedEventId(): ?int
    {
        return null;
    }

    protected function onEventSaved(Event $event): void {}

    protected function attemptEvent(callable $callback, ?string $success = null): bool
    {
        try {
            $callback();
            if ($success) {
                Notification::make()->success()->title($success)->send();
            }

            return true;
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();

            return false;
        }
    }

    public function eventAction(string $name): Action
    {
        $editing = $name === 'editEvent';

        return Action::make($name)
            ->modalHeading($editing ? 'Edit event' : 'New event')
            ->modalWidth(Width::TwoExtraLarge)
            ->modalSubmitActionLabel($editing ? 'Save' : 'Create')
            ->fillForm(function (): array {
                // Next full hour today, or tomorrow morning once the working day is over.
                $start = CarbonImmutable::now()->addHour()->startOfHour();
                if ($start->hour >= 18 || $start->hour < 8) {
                    $start = CarbonImmutable::today()->addDay()->setTime(9, 0);
                }

                return [
                    'kind' => 'meeting', 'date' => $start->toDateString(), 'from' => $start->format('H:i'),
                    'to'   => $start->addHour()->format('H:i'), 'all_day' => false, 'attendees' => [],
                ];
            })
            ->schema([
                ToggleButtons::make('kind')->hiddenLabel()->inline()->live()->required()
                    ->options(collect(Calendar::KINDS)->map(fn ($k) => $k[0])->all())
                    ->icons(collect(Calendar::KINDS)->map(fn ($k) => $k[1])->all())
                    ->afterStateUpdated(fn (?string $state, Set $set) => $set('all_day', (bool) (Calendar::KINDS[$state ?? 'meeting'][3] ?? false))),
                TextInput::make('title')->label('Title')->maxLength(200)
                    ->required(fn (Get $get) => $get('kind') === 'meeting')
                    ->placeholder(fn (Get $get) => Calendar::kind((string) $get('kind'))[0]),
                Grid::make(4)->schema([
                    DatePicker::make('date')->label(fn (Get $get) => $get('all_day') ? 'From' : 'Day')->required()->native(false)->displayFormat('D d/m/Y')->live(),
                    DatePicker::make('date_to')->label('Until')->native(false)->displayFormat('D d/m/Y')->visible(fn (Get $get) => (bool) $get('all_day'))->live(),
                    TimePicker::make('from')->label('From')->seconds(false)->required(fn (Get $get) => ! $get('all_day'))->hidden(fn (Get $get) => (bool) $get('all_day'))->live(),
                    TimePicker::make('to')->label('To')->seconds(false)->required(fn (Get $get) => ! $get('all_day'))->hidden(fn (Get $get) => (bool) $get('all_day'))->live(),
                ]),
                Toggle::make('all_day')->label('All day')->live(),
                Select::make('attendees')->label(fn (Get $get) => Calendar::kind((string) $get('kind'))[3] ? 'Who (you if empty)' : 'Invite people')
                    ->multiple()->searchable()->live()
                    ->options(fn (): array => Calendar::people()->reject(fn (User $u) => $u->is($this->calendarUser()))->pluck('name', 'id')->all()),
                Text::make(fn (Get $get): HtmlString|string => $this->conflictText($get))->visible(fn (Get $get) => filled($get('attendees'))),
                Grid::make(2)->schema([
                    TextInput::make('location')->label('Where or link')->maxLength(255),
                    Select::make('project_id')->label('Project')->searchable()->options(fn (): array => Project::query()->orderBy('name')->pluck('name', 'id')->all()),
                ]),
                Textarea::make('description')->label('Notes')->rows(3),
                Toggle::make('private')->label('Private (others only see “Busy”)'),
            ])
            ->action(function (array $data) use ($editing): void {
                $event = $editing && $this->editedEventId() ? Event::query()->find($this->editedEventId()) : null;
                $saved = null;
                $this->attemptEvent(function () use ($data, $event, &$saved): void {
                    $saved = Calendar::save($this->calendarUser(), $data, $event);
                }, $editing ? 'Event updated' : 'Event created');
                if ($saved) {
                    $this->onEventSaved($saved);
                }
            });
    }

    private function conflictText(Get $get): HtmlString|string
    {
        try {
            [$start, $end] = Calendar::range(['date' => $get('date'), 'date_to' => $get('date_to'), 'all_day' => $get('all_day'), 'from' => $get('from'), 'to' => $get('to')]);
        } catch (\Throwable) {
            return '';
        }
        $conflicts = Calendar::conflicts((array) $get('attendees'), $start, $end, $this->mountedActionEventId());
        if (! $conflicts) {
            return new HtmlString('<span class="hv-cal-free">✓ Everyone is free at this time</span>');
        }

        return new HtmlString('<div class="hv-cal-conflicts"><b>Already busy:</b><ul>'.collect($conflicts)->map(fn ($c) => '<li>'.e($c).'</li>')->implode('').'</ul></div>');
    }

    private function mountedActionEventId(): ?int
    {
        return ($this->mountedActions[0]['name'] ?? null) === 'editEvent' ? $this->editedEventId() : null;
    }
}
