<?php

namespace Huvant\Orders\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns this plugin adds to contacts and products. Plugin migrations can run before those
 * tables exist (fresh installs, tests), so the install command applies them again.
 */
class OrdersSchema
{
    /** @var array<string, array<string, string>> */
    private const COLUMNS = [
        'partners_partners' => [
            'huvant_sdi_code'       => 'string:7',
            'huvant_pec'            => 'string:255',
            'huvant_customs_code'   => 'string:60',
            'huvant_event_date'     => 'date',
            'huvant_onsite_contact' => 'string:160',
        ],
        'products_products' => [
            'huvant_hs_code'            => 'string:20',
            'huvant_production_days'    => 'smallint',
            'huvant_role'               => 'string:20',
            'huvant_code_prefix'        => 'string:12',
            'huvant_shelf_life'         => 'smallint',
            'huvant_shelf_life_unit'    => 'string:10',
            'huvant_package_unit'       => 'string:20',
            'huvant_min_quantity'       => 'decimal4',
            'huvant_package_price'      => 'decimal',
            'huvant_cas_number'         => 'string:30',
            'huvant_lab_kind'           => 'string:20',
            'huvant_lab_use'            => 'string:20',
            'huvant_supplier'           => 'string:120',
            'huvant_supplier_code'      => 'string:60',
            'huvant_package_quantity'   => 'decimal4',
            'huvant_package_uom_id'     => 'foreign',
            'huvant_density'            => 'decimal4',
            'huvant_storage_position'   => 'string:120',
        ],
        // Hours the lab spent on a manufacturing order, for the margin.
        'manufacturing_orders' => [
            'huvant_worked_hours' => 'decimal',
        ],
        // Outgoing transfers raised by an order carry what the delivery note and the courier need.
        'inventories_operations' => [
            'huvant_order_id'             => 'foreign',
            'huvant_delivery_note_number' => 'string:30',
            'huvant_carrier'              => 'string:80',
            'huvant_tracking_number'      => 'string:120',
            'huvant_shipping_cost'        => 'decimal',
            'huvant_packages'             => 'smallint',
            'huvant_weight_kg'            => 'decimal',
            'huvant_shipped_at'           => 'date',
            'huvant_delivered_at'         => 'date',
            'huvant_shipping_status'      => 'string:20',
        ],
    ];

    public static function ensureColumns(): void
    {
        foreach (self::COLUMNS as $tableName => $columns) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            $missing = array_filter($columns, fn (string $column): bool => ! Schema::hasColumn($tableName, $column), ARRAY_FILTER_USE_KEY);

            if ($missing === []) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($missing): void {
                foreach ($missing as $column => $type) {
                    [$kind, $length] = array_pad(explode(':', $type), 2, null);

                    match ($kind) {
                        'string'   => $table->string($column, (int) $length)->nullable(),
                        'date'     => $table->date($column)->nullable(),
                        'smallint' => $table->unsignedSmallInteger($column)->nullable(),
                        'decimal'  => $table->decimal($column, 15, 2)->nullable(),
                        'decimal4' => $table->decimal($column, 15, 4)->nullable(),
                        'foreign'  => $table->unsignedBigInteger($column)->nullable()->index(),
                    };
                }
            });
        }
    }

    public static function dropColumns(): void
    {
        foreach (self::COLUMNS as $tableName => $columns) {
            $present = array_values(array_filter(array_keys($columns), fn (string $column): bool => Schema::hasColumn($tableName, $column)));

            if ($present !== []) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn($present));
            }
        }
    }
}
