<?php

namespace Huvant\Orders\Console;

use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Support\LabStock;
use Huvant\Orders\Support\LabUnits;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Shipping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Enums\AddressType;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\UOM;

/**
 * One-off import of the lab's Excel registers (customers, delivery addresses, products and lab items),
 * from the JSON extracted out of them. Safe to run again: records are matched by name or code and
 * only filled in. People are never created here: external contacts go through the structured flow.
 */
class ImportRegisters extends Command
{
    protected $signature = 'huvant:import-registers {file : JSON extracted from the Excel registers} {--dry-run : Show what would change, write nothing} {--as= : Email of the user recorded as creator (default: the first administrator)}';

    protected $description = 'Import customers, delivery addresses, products and lab items from the lab Excel registers';

    /** @var array<string, int> */
    private array $counts = [];

    public function handle(): int
    {
        $data = json_decode((string) file_get_contents($this->argument('file')), true, flags: JSON_THROW_ON_ERROR);

        // Records keep a creator: the import runs as an administrator.
        $user = $this->option('as')
            ? User::query()->where('email', $this->option('as'))->firstOrFail()
            : User::query()->get()->first(fn (User $user): bool => Orders::canSeePrices($user));
        Auth::login($user ?? throw new \RuntimeException('No administrator to run the import as.'));

        LabUnits::ensureUnits();

        DB::beginTransaction();

        try {
            $this->importCompanies($data['companies'] ?? []);
            $this->importAddresses($data['addresses'] ?? []);
            $this->importProducts($data['products'] ?? []);
            $this->importLabItems($data['lab_items'] ?? []);
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        $this->option('dry-run') ? DB::rollBack() : DB::commit();

        foreach ($this->counts as $what => $count) {
            $this->line(sprintf('%-28s %d', $what, $count));
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run: nothing was written.');
        }

        return self::SUCCESS;
    }

    private function count(string $what): void
    {
        $this->counts[$what] = ($this->counts[$what] ?? 0) + 1;
    }

    private function country(?string $name): ?int
    {
        return $name ? Country::query()->where('name', $name)->value('id') : null;
    }

    private function company(string $name): ?Partner
    {
        return Partner::query()
            ->where('account_type', AccountType::COMPANY)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($name))])
            ->first();
    }

    /** @param  array<int, array<string, mixed>>  $companies */
    private function importCompanies(array $companies): void
    {
        foreach ($companies as $row) {
            $company = $this->company($row['name']);
            $this->count($company ? 'companies updated' : 'companies created');

            $company ??= new Partner(['account_type' => AccountType::COMPANY, 'name' => $row['name']]);

            // Only blanks are filled: what is already in the ERP wins.
            foreach ([
                'street1'             => $row['street1'],
                'zip'                 => $row['zip'],
                'city'                => trim(implode(' ', array_filter([$row['city'], $row['province'] ? "({$row['province']})" : null]))) ?: null,
                'country_id'          => $this->country($row['country']),
                'tax_id'              => $row['tax_id'],
                'phone'               => $row['phone'],
                'huvant_sdi_code'     => $row['sdi'],
                'huvant_customs_code' => $row['customs'],
            ] as $field => $value) {
                if (blank($company->{$field}) && filled($value)) {
                    $company->{$field} = $value;
                }
            }

            $company->save();
        }
    }

    /** @param  array<int, array<string, mixed>>  $addresses */
    private function importAddresses(array $addresses): void
    {
        foreach ($addresses as $row) {
            $company = $this->company((string) $row['company']);

            if (! $company) {
                $this->count('addresses skipped');

                continue;
            }

            Partner::query()->firstOrCreate(
                ['account_type' => AccountType::ADDRESS, 'sub_type' => AddressType::DELIVERY->value, 'parent_id' => $company->id, 'name' => $row['name']],
                [
                    'street1'               => $row['street1'],
                    'zip'                   => $row['zip'],
                    'city'                  => trim(implode(' ', array_filter([$row['city'], $row['province'] ? "({$row['province']})" : null]))) ?: null,
                    'country_id'            => $this->country($row['country']),
                    'huvant_onsite_contact' => $row['onsite_contact'],
                ],
            )->wasRecentlyCreated ? $this->count('addresses created') : $this->count('addresses already there');
        }
    }

    /**
     * Simulators, kits and inserts are products; rentals and staff keep their role.
     *
     * @param  array<int, array<string, mixed>>  $products
     */
    private function importProducts(array $products): void
    {
        $units = UOM::query()->where('name', 'Units')->firstOrFail();

        foreach ($products as $row) {
            $product = Product::query()
                ->when($row['reference'], fn ($query) => $query->where('reference', $row['reference']), fn ($query) => $query->where('name', $row['name']))
                ->first();

            if ($product) {
                $this->count('products already there');

                continue;
            }

            // The role sets stock type and tracking (ItemRoles): pads and kits by lot, rentals as pieces.
            $product = Product::query()->create([
                'huvant_role'            => match ($row['kind']) {
                    'rental'  => ItemRole::Rental,
                    'service' => ItemRole::Service,
                    default   => ItemRole::Product,
                },
                'name'                   => $row['name'],
                'reference'              => $row['reference'],
                'price'                  => $row['price'] ?? 0,
                'uom_id'                 => $units->id,
                'uom_po_id'              => $units->id,
                'category_id'            => $this->category($row['category']),
                'huvant_production_days' => $row['production_days'],
                'huvant_hs_code'         => $row['hs_code'] ?? null,
            ]);

            $this->count('products created');
        }
    }

    /**
     * Reagents and the rest of the lab inventory: lot tracked with expiry dates, minimum stock as a
     * reordering rule. Stock levels are not imported (the sheet did not hold them reliably): they come
     * with the first count.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importLabItems(array $items): void
    {
        $units = UOM::query()->where('name', 'Units')->firstOrFail();
        $warehouse = Shipping::warehouse(current_company_id());

        foreach ($items as $row) {
            if (Product::query()->where('reference', $row['reference'])->exists()) {
                $this->count('lab items already there');

                continue;
            }

            // "250 mL": stocked in mL, a package holds 250. Without a size, counted in units.
            [$packageQuantity, $packageUnit] = LabUnits::parsePackage($row['package']) ?? [null, null];
            $mainUnit = $packageUnit ?? $units;

            $product = Product::query()->create([
                'huvant_role'             => ItemRole::Material,
                'name'                    => $row['name'],
                'reference'               => $row['reference'],
                'price'                   => 0,
                'uom_id'                  => $mainUnit->id,
                'uom_po_id'               => $mainUnit->id,
                'huvant_package_quantity' => $packageQuantity,
                'huvant_package_uom_id'   => $packageUnit?->id,
                'category_id'             => $this->category('Lab'),
                'enable_purchase'         => true,
                'description_purchase'    => $row['notes'],
                'huvant_lab_kind'         => $row['kind'],
                'huvant_lab_use'          => $row['use'] === 'research' ? LabStock::RESEARCH : ($row['use'] === 'production' ? LabStock::PRODUCTION : null),
                'huvant_cas_number'       => $row['cas'],
                'huvant_supplier'         => $row['supplier'],
                'huvant_supplier_code'    => $row['supplier_code'],
                'huvant_storage_position' => $row['position'],
            ]);

            // The sheet counted the minimum in packages.
            if ($row['minimum'] !== null) {
                LabStock::setMinimum(InventoryProduct::query()->findOrFail($product->id), $warehouse, (float) $row['minimum'] * ($packageQuantity ?? 1));
            }

            $this->count('lab items created');
        }
    }

    private function category(?string $name): ?int
    {
        if (blank($name)) {
            return Category::query()->orderBy('id')->value('id');
        }

        return Category::query()->firstOrCreate(['name' => $name])->id;
    }
}
