<x-filament-panels::page>
    <div class="flex items-center gap-3">
        <x-filament::button color="gray" size="sm" icon="heroicon-m-chevron-left" wire:click="shiftMonth(-1)" />
        <span class="text-lg font-semibold">{{ ucfirst($monthLabel) }}</span>
        <x-filament::button color="gray" size="sm" icon="heroicon-m-chevron-right" wire:click="shiftMonth(1)" />
    </div>

    @if ($categories->isEmpty())
        <x-filament::section>
            No rental categories yet: create them under Rental categories, then assign products to them.
        </x-filament::section>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
            <table class="w-full text-xs">
                <thead>
                <tr class="bg-gray-50 dark:bg-white/5">
                    <th class="sticky left-0 bg-gray-50 px-3 py-2 text-left dark:bg-gray-900">Category</th>
                    @foreach ($days as $day)
                        <th @class(['px-1 py-2 text-center font-medium', 'text-gray-400' => $day->isWeekend(), 'text-primary-600' => $day->isToday()])>
                            {{ $day->format('j') }}
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($categories as $category)
                    <tr class="border-t border-gray-100 dark:border-white/5">
                        <td class="sticky left-0 bg-white px-3 py-2 dark:bg-gray-900">
                            <span class="font-medium">{{ $category->name }}</span>
                            <span class="text-gray-500">· {{ $category->units }}</span>
                        </td>
                        @foreach ($days as $day)
                            @php
                                $taken = $occupancy[$category->id][$day->toDateString()] ?? 0;
                                $state = $taken === 0 ? 'free' : ($taken > $category->units ? 'over' : ($taken === $category->units ? 'full' : 'partial'));
                            @endphp
                            <td class="px-0.5 py-1 text-center">
                                <div @class([
                                    'rounded py-1',
                                    'bg-gray-50 text-gray-300 dark:bg-white/5 dark:text-gray-600' => $state === 'free',
                                    'bg-primary-100 text-primary-700 dark:bg-primary-500/20 dark:text-primary-300' => $state === 'partial',
                                    'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-300' => $state === 'full',
                                    'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-300' => $state === 'over',
                                ]) title="{{ $taken }} / {{ $category->units }}">
                                    {{ $taken ?: '·' }}
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($categories as $category)
                @continue(! isset($bookings[$category->id]))
                <x-filament::section :heading="$category->name" compact>
                    <ul class="space-y-1 text-sm">
                        @foreach ($bookings[$category->id] as $line)
                            <li>
                                <a href="{{ $orderUrl($line) }}" class="font-medium text-primary-600 hover:underline">{{ $line->order->order_number ?? $line->order->name }}</a>
                                · {{ $line->order->partner?->name ?? 'Stock' }}
                                @if ($line->order->event) · {{ $line->order->event }} @endif
                                · {{ $line->order->rental_starts_on->format('d/m') }}–{{ $line->order->rental_ends_on->format('d/m') }}
                                · {{ rtrim(rtrim(number_format((float) $line->quantity, 2, ',', ''), '0'), ',') }} × {{ $line->product->name }}
                                @if ($line->order->state === \Huvant\Orders\Enums\OrderState::Pending)
                                    <x-filament::badge color="warning" size="sm" class="inline-flex">on hold</x-filament::badge>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-filament::section>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
