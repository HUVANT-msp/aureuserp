<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Document numbers as PREFIX-YYYYMMDD-NNN: the document's date, then a counter that runs through
 * the year and starts again on 1 January (as Italian delivery notes require).
 */
class DocumentNumber
{
    public const OFFER = 'OFF';

    public const ORDER = 'ORD';

    public const DELIVERY_NOTE = 'DDT';

    public static function next(string $prefix, CarbonInterface $date): string
    {
        return DB::transaction(function () use ($prefix, $date): string {
            $year = (int) $date->format('Y');

            DB::table('huvant_document_numbers')->insertOrIgnore([
                'prefix' => $prefix, 'year' => $year, 'last_number' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);

            $counter = DB::table('huvant_document_numbers')->where('prefix', $prefix)->where('year', $year)->lockForUpdate()->first();
            $number = $counter->last_number + 1;

            DB::table('huvant_document_numbers')->where('id', $counter->id)->update(['last_number' => $number, 'updated_at' => now()]);

            return sprintf('%s-%s-%03d', $prefix, $date->format('Ymd'), $number);
        });
    }
}
