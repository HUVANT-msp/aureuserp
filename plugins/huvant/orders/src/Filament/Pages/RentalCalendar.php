<?php

namespace Huvant\Orders\Filament\Pages;

use BackedEnum;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Huvant\Orders\Filament\Resources\OrderResource;
use Huvant\Orders\Models\OrderLine;
use Huvant\Orders\Support\Rentals;
use Illuminate\Support\Carbon;
use Webkul\Support\Enums\NavigationGroup;

/** One row per rental item, one column per day of the month: pieces booked against pieces owned. */
class RentalCalendar extends Page
{
    protected string $view = 'huvant-orders::filament.pages.rental-calendar';

    protected static ?string $slug = 'huvant/rental-calendar';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 2;

    public string $month;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }

    public static function getNavigationLabel(): string
    {
        return 'Rental calendar';
    }

    public function getTitle(): string
    {
        return 'Rental calendar';
    }

    public function mount(): void
    {
        $this->month = today()->format('Y-m');
    }

    public function shiftMonth(int $months): void
    {
        $this->month = Carbon::createFromFormat('Y-m-d', $this->month.'-01')->addMonths($months)->format('Y-m');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $from = Carbon::createFromFormat('Y-m-d', $this->month.'-01')->startOfDay();
        $until = $from->copy()->endOfMonth()->startOfDay();

        return [
            'monthLabel' => $from->translatedFormat('F Y'),
            'days'       => collect(CarbonPeriod::create($from, $until))->map(fn ($day) => Carbon::instance($day))->all(),
            'items'      => Rentals::items(),
            'units'      => Rentals::items()->mapWithKeys(fn ($item): array => [$item->id => Rentals::units($item)])->all(),
            'occupancy'  => Rentals::occupancy($from, $until),
            'bookings'   => Rentals::bookings($from, $until)
                ->sortBy(fn (OrderLine $line) => $line->order->rental_starts_on)
                ->groupBy('product_id'),
            'orderUrl'   => fn (OrderLine $line): string => OrderResource::getUrl('edit', ['record' => $line->order]),
        ];
    }
}
