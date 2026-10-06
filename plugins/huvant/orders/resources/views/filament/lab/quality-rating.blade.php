@php
    $rating = (int) ($record->quality_rating ?? 0);
    $editable = ! $record->trashed()
        && $record->status === \Huvant\Orders\Enums\UnitStatus::InLab
        && $record->order_id === null
        && $record->order_line_id === null;
@endphp

<div
    class="flex min-w-28 items-center gap-0.5"
    role="{{ $editable ? 'radiogroup' : 'img' }}"
    aria-label="{{ __('huvant-orders::lab.quality_rating', ['code' => $record->code, 'rating' => $rating]) }}"
>
    @foreach (range(1, 5) as $star)
        @if ($editable)
            <button
                type="button"
                wire:key="quality-{{ $record->id }}-{{ $star }}"
                wire:click.stop="rateUnit({{ $record->id }}, {{ $star }})"
                wire:loading.attr="disabled"
                wire:target="rateUnit({{ $record->id }}, {{ $star }})"
                role="radio"
                aria-checked="{{ $rating === $star ? 'true' : 'false' }}"
                aria-label="{{ __('huvant-orders::lab.set_quality_rating', ['code' => $record->code, 'rating' => $star]) }}"
                title="{{ __('huvant-orders::lab.set_quality_rating', ['code' => $record->code, 'rating' => $star]) }}"
                class="group rounded-sm p-0.5 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:cursor-wait disabled:opacity-50"
            >
                <x-filament::icon
                    :icon="$star <= $rating ? 'heroicon-s-star' : 'heroicon-o-star'"
                    @class([
                        'h-5 w-5 transition-colors',
                        'text-amber-400 dark:text-amber-300' => $star <= $rating,
                        'text-gray-300 group-hover:text-amber-300 dark:text-gray-600 dark:group-hover:text-amber-400' => $star > $rating,
                    ])
                />
            </button>
        @else
            <x-filament::icon
                :icon="$star <= $rating ? 'heroicon-s-star' : 'heroicon-o-star'"
                @class([
                    'h-5 w-5',
                    'text-amber-400 dark:text-amber-300' => $star <= $rating,
                    'text-gray-300 dark:text-gray-600' => $star > $rating,
                ])
                aria-hidden="true"
            />
        @endif
    @endforeach
</div>
