<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\ManufacturingSource;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\ProductionTaskStatus;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Models\ManufacturingEntry;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\OrderLine;
use Huvant\Orders\Models\ProductionTask;
use Huvant\Orders\Models\ProductUnit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Webkul\Product\Models\Product;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;

/** Turns an accepted offer into one traceable sourcing decision for every physical piece. */
class ManufacturingFlow
{
    public static function stockAvailable(Product|int $product): int
    {
        return ProductUnit::query()
            ->where('product_id', $product instanceof Product ? $product->getKey() : $product)
            ->where('status', UnitStatus::InLab)
            ->count();
    }

    /** @return Collection<int, ProductUnit> */
    public static function availableStockUnits(Product|int $product): Collection
    {
        return ProductUnit::query()
            ->with('creator:id,name')
            ->where('product_id', $product instanceof Product ? $product->getKey() : $product)
            ->where('status', UnitStatus::InLab)
            ->orderByRaw('expiry_date is null, expiry_date')
            ->orderBy('production_date')
            ->orderBy('id')
            ->get();
    }

    /** @return Collection<int, OrderLine> */
    public static function productLines(Order $order): Collection
    {
        return $order->lines()
            ->with('product')
            ->whereHas('product', fn ($query) => $query->where('huvant_role', ItemRole::Product->value))
            ->get();
    }

    /** @return Collection<int, ManufacturingEntry> */
    public static function prepareEntries(Order $order): Collection
    {
        if (! Schema::hasTable('huvant_manufacturing_entries')) {
            return collect();
        }

        DB::transaction(function () use ($order): void {
            foreach (static::productLines($order) as $line) {
                foreach (range(1, static::pieces($line)) as $position) {
                    ManufacturingEntry::query()->firstOrCreate([
                        'order_line_id' => $line->getKey(),
                        'position'      => $position,
                    ], [
                        'order_id'   => $order->getKey(),
                        'product_id' => $line->product_id,
                    ]);
                }
            }
        });

        return static::entries($order);
    }

    /** @return Collection<int, ManufacturingEntry> */
    public static function entries(Order $order): Collection
    {
        return $order->manufacturingEntries()
            ->with([
                'product',
                'productUnit.creator:id,name',
                'productUnit.materials.material',
                'productUnit.materials.lot',
                'productionTask.projectTask.users:id,name',
                'productionTask.projectTask.project',
                'managedBy:id,name',
            ])
            ->orderBy('order_line_id')
            ->orderBy('position')
            ->get();
    }

    public static function assignStock(ManufacturingEntry $entry, ProductUnit $unit, ?User $user = null): ManufacturingEntry
    {
        $user ??= Auth::user();

        return DB::transaction(function () use ($entry, $unit, $user): ManufacturingEntry {
            $locked = ManufacturingEntry::query()->with(['order', 'productionTask.projectTask'])->lockForUpdate()->findOrFail($entry->getKey());
            static::assertEditable($locked);

            $stockUnit = ProductUnit::query()->lockForUpdate()->findOrFail($unit->getKey());
            if ((int) $stockUnit->product_id !== (int) $locked->product_id || $stockUnit->status !== UnitStatus::InLab) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_stock_unit_unavailable'));
            }

            static::releaseCurrentUnit($locked);
            static::removeProductionChoice($locked);

            $stockUnit->update([
                'status'        => UnitStatus::Sold,
                'status_since'  => today(),
                'order_id'      => $locked->order_id,
                'order_line_id' => $locked->order_line_id,
            ]);
            $locked->update([
                'source'          => ManufacturingSource::Stock,
                'product_unit_id' => $stockUnit->getKey(),
                'managed_at'      => now(),
                'managed_by'      => $user?->getKey(),
            ]);

            return $locked->refresh();
        });
    }

    public static function assignProduction(ManufacturingEntry $entry, array $userIds, CarbonInterface $deadline, ?User $user = null): ManufacturingEntry
    {
        $user ??= Auth::user();

        return DB::transaction(function () use ($entry, $userIds, $deadline, $user): ManufacturingEntry {
            $locked = ManufacturingEntry::query()->with(['order', 'product', 'productionTask.projectTask'])->lockForUpdate()->findOrFail($entry->getKey());
            static::assertEditable($locked);

            if ($deadline->startOfDay()->isBefore(today()) && ! $locked->order->expected_delivery_date?->isBefore(today())) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_deadline_past'));
            }
            if ($locked->order->expected_delivery_date && $deadline->startOfDay()->isAfter($locked->order->expected_delivery_date)) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_deadline_after_offer'));
            }

            $assignees = ProductionProjects::validLabUserIds($userIds);
            static::releaseCurrentUnit($locked);

            $production = $locked->productionTask;
            if (! $production || $production->status === ProductionTaskStatus::Cancelled) {
                $production = ProductionTask::query()->create([
                    'order_id'               => $locked->order_id,
                    'order_line_id'          => $locked->order_line_id,
                    'manufacturing_entry_id' => $locked->getKey(),
                    'product_id'             => $locked->product_id,
                    'quantity'               => 1,
                    'due_date'               => $deadline->toDateString(),
                    'managed_by'             => $user?->getKey(),
                ]);
                ProductionProjects::createTask($production->load(['product', 'order', 'manufacturingEntry']), $assignees, $deadline);
            } else {
                if ($production->status === ProductionTaskStatus::Completed) {
                    throw new RuntimeException(__('huvant-orders::manufacturing.error_completed_choice'));
                }
                $production->update(['due_date' => $deadline->toDateString(), 'managed_by' => $user?->getKey()]);
                $production->projectTask?->forceFill(['deadline' => $deadline])->save();
                $production->projectTask?->users()->sync($assignees);
            }

            $locked->update([
                'source'          => ManufacturingSource::Production,
                'product_unit_id' => null,
                'managed_at'      => now(),
                'managed_by'      => $user?->getKey(),
            ]);

            return $locked->refresh();
        });
    }

    public static function finalize(Order $order, ?User $user = null): Order
    {
        $user ??= Auth::user();

        $managed = DB::transaction(function () use ($order, $user): Order {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            if ($locked->state !== OrderState::Confirmed) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_only_confirmed'));
            }
            if ($locked->manufacturing_managed_at) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_already_managed'));
            }

            $entries = static::prepareEntries($locked);
            if ($entries->contains(fn (ManufacturingEntry $entry): bool => ! $entry->isManaged())) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_entries_unmanaged'));
            }

            foreach (static::productLines($locked) as $line) {
                $lineEntries = $entries->where('order_line_id', $line->getKey());
                $line->update([
                    'stock_quantity'           => $lineEntries->where('source', ManufacturingSource::Stock)->count(),
                    'production_quantity'      => $lineEntries->where('source', ManufacturingSource::Production)->count(),
                    'manufacturing_managed_at' => now(),
                    'manufacturing_managed_by' => $user?->getKey(),
                ]);
            }

            $locked->update([
                'manufacturing_managed_at' => now(),
                'manufacturing_managed_by' => $user?->getKey(),
            ]);

            return $locked->refresh();
        });

        static::refreshOrderCompletion($managed);
        $order->setRawAttributes($managed->fresh()->getAttributes(), true);

        return $order;
    }

    /**
     * Compatibility for callers that allocate a quantity instead of individual entries.
     *
     * @param  array<int|string, int|string|null>  $stockQuantities
     */
    public static function manage(Order $order, array $stockQuantities, ?User $user = null): Order
    {
        $user ??= Auth::user();
        $entries = static::prepareEntries($order);

        foreach (static::productLines($order) as $line) {
            $fromStock = (int) ($stockQuantities[$line->getKey()] ?? 0);
            $lineEntries = $entries->where('order_line_id', $line->getKey())->values();
            if ($fromStock < 0 || $fromStock > $lineEntries->count()) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_stock_range', ['product' => $line->product->name]));
            }

            $units = static::availableStockUnits($line->product_id)->take($fromStock)->values();
            if ($units->count() !== $fromStock) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_stock_changed', [
                    'product'   => $line->product->name,
                    'available' => $units->count(),
                ]));
            }

            foreach ($lineEntries as $index => $entry) {
                if ($index < $fromStock) {
                    static::assignStock($entry, $units[$index], $user);

                    continue;
                }

                $assignees = ProductionProjects::labUsers()->pluck('id')->all();
                if ($assignees === [] && $user) {
                    $assignees = [$user->getKey()];
                }
                static::assignProduction($entry, $assignees, $order->expected_delivery_date ?? today()->addDays(max(1, (int) $line->product->huvant_production_days)), $user);
            }
        }

        return static::finalize($order, $user);
    }

    /** @return Collection<int, ProductUnit> */
    public static function completeProduction(ProductionTask $task, int $pieces, CarbonInterface $date, array $lots, ?string $notes = null, ?User $user = null, bool $syncProjectTask = true): Collection
    {
        $user ??= Auth::user();

        $units = DB::transaction(function () use ($task, $pieces, $date, $lots, $notes, $user): Collection {
            $lockedTask = ProductionTask::query()->with(['product', 'orderLine', 'manufacturingEntry'])->lockForUpdate()->findOrFail($task->getKey());

            if ($lockedTask->status !== ProductionTaskStatus::Pending || $pieces < 1 || $pieces > $lockedTask->remaining()) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_production_quantity'));
            }

            $units = LabInventory::produce($lockedTask->product, $date, $pieces, $lots, $notes);
            foreach ($units as $unit) {
                $unit->update([
                    'status'        => UnitStatus::Sold,
                    'status_since'  => $date->toDateString(),
                    'order_id'      => $lockedTask->order_id,
                    'order_line_id' => $lockedTask->order_line_id,
                ]);
            }

            $completed = $lockedTask->completed_quantity + $pieces;
            $done = $completed >= $lockedTask->quantity;
            $lockedTask->update([
                'completed_quantity' => $completed,
                'status'             => $done ? ProductionTaskStatus::Completed : ProductionTaskStatus::Pending,
                'completed_at'       => $done ? now() : null,
                'managed_by'         => $user?->getKey(),
                'notes'              => $notes ?: $lockedTask->notes,
            ]);

            if ($done && $lockedTask->manufacturingEntry) {
                $lockedTask->manufacturingEntry->update(['product_unit_id' => $units->last()->getKey()]);
            }

            return $units;
        });

        $task->refresh();
        if ($syncProjectTask && $task->status === ProductionTaskStatus::Completed) {
            ProductionProjects::syncTaskCompleted($task->load('projectTask.project'));
        }
        static::refreshOrderCompletion($task->order);

        return $units;
    }

    public static function completeFromProjectTask(ProductionTask $production): void
    {
        $production->loadMissing(['product', 'order']);
        $lots = [];

        foreach (LabInventory::recipe($production->product) as $line) {
            $needed = (float) $line->quantity * $production->remaining();
            $lot = LabInventory::availableLots($line->material_id)
                ->first(fn ($candidate): bool => (float) $candidate->remaining_quantity + 0.00001 >= $needed);
            if (! $lot) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_no_automatic_lot', ['material' => $line->material->name]));
            }
            $lots[$line->material_id] = $lot->getKey();
        }

        static::completeProduction(
            $production,
            $production->remaining(),
            today(),
            $lots,
            __('huvant-orders::manufacturing.completed_from_task'),
            Auth::user(),
            false,
        );
    }

    public static function syncFromProjectTask(Task $task, ?TaskState $state): void
    {
        if (! $task->exists || ! Schema::hasColumn('huvant_production_tasks', 'project_task_id')) {
            return;
        }

        $production = ProductionTask::query()->where('project_task_id', $task->getKey())->first();
        if (! $production) {
            return;
        }

        if ($task->isDirty('deadline')) {
            $deadline = $task->deadline ? Carbon::parse($task->deadline)->startOfDay() : null;
            if ($deadline && $production->order->expected_delivery_date && $deadline->isAfter($production->order->expected_delivery_date)) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_deadline_after_offer'));
            }
            $production->update(['due_date' => $deadline?->toDateString()]);
        }

        if (! $state) {
            return;
        }

        if ($state === TaskState::DONE) {
            if ($production->status !== ProductionTaskStatus::Completed) {
                static::completeFromProjectTask($production);
            }

            return;
        }

        if ($production->status === ProductionTaskStatus::Completed) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_completed_task_reopen'));
        }

        $production->update([
            'status' => $state === TaskState::CANCELLED ? ProductionTaskStatus::Cancelled : ProductionTaskStatus::Pending,
        ]);
        static::refreshOrderCompletion($production->order);
    }

    public static function orderIsComplete(Order $order): bool
    {
        if (! $order->manufacturing_managed_at) {
            return false;
        }

        return static::entries($order)->every(fn (ManufacturingEntry $entry): bool => $entry->isComplete());
    }

    public static function refreshOrderCompletion(Order $order): void
    {
        $order->refresh();
        $completed = static::orderIsComplete($order);

        $order->forceFill([
            'manufacturing_completed_at' => $completed ? ($order->manufacturing_completed_at ?? now()) : null,
        ])->save();
    }

    public static function release(Order $order): void
    {
        ProductUnit::query()
            ->where('order_id', $order->getKey())
            ->whereIn('status', [UnitStatus::Allocated->value, UnitStatus::Sold->value])
            ->update([
                'status'        => UnitStatus::InLab->value,
                'status_since'  => today(),
                'order_id'      => null,
                'order_line_id' => null,
            ]);

        $order->productionTasks()->with('projectTask.project')->get()->each(function (ProductionTask $task): void {
            $task->update(['status' => ProductionTaskStatus::Cancelled]);
            ProductionProjects::syncTaskCancelled($task);
        });
        $order->manufacturingEntries()->update(['product_unit_id' => null]);
        $order->update(['manufacturing_completed_at' => null]);
    }

    public static function backfill(): void
    {
        if (! Schema::hasTable('huvant_manufacturing_entries')) {
            return;
        }

        Order::query()->where('state', OrderState::Confirmed->value)->orderBy('id')->each(function (Order $order): void {
            $entries = static::prepareEntries($order);
            if (! $order->manufacturing_managed_at) {
                return;
            }

            foreach (static::productLines($order) as $line) {
                $lineEntries = $entries->where('order_line_id', $line->getKey())->values();
                $units = ProductUnit::query()->where('order_line_id', $line->getKey())->orderBy('id')->get();
                $stockCount = (int) $line->stock_quantity;

                foreach ($lineEntries as $index => $entry) {
                    $unit = $units->get($index);
                    $source = $index < $stockCount ? ManufacturingSource::Stock : ManufacturingSource::Production;
                    $entry->update([
                        'source'          => $source,
                        'product_unit_id' => $unit?->getKey(),
                        'managed_at'      => $line->manufacturing_managed_at ?? $order->manufacturing_managed_at,
                        'managed_by'      => $line->manufacturing_managed_by ?? $order->manufacturing_managed_by,
                    ]);
                    $unit?->update(['status' => UnitStatus::Sold]);

                    if ($source === ManufacturingSource::Production) {
                        $task = ProductionTask::query()->firstOrCreate([
                            'manufacturing_entry_id' => $entry->getKey(),
                        ], [
                            'order_id'           => $order->getKey(),
                            'order_line_id'      => $line->getKey(),
                            'product_id'         => $line->product_id,
                            'quantity'           => 1,
                            'due_date'           => $order->expected_delivery_date,
                            'completed_quantity' => $unit ? 1 : 0,
                            'status'             => $unit ? ProductionTaskStatus::Completed : ProductionTaskStatus::Pending,
                            'completed_at'       => $unit ? now() : null,
                            'managed_by'         => $order->manufacturing_managed_by,
                        ]);
                        if (! $task->project_task_id && ProductionProjects::labUsers()->isNotEmpty()) {
                            ProductionProjects::createTask($task->load(['product', 'order', 'manufacturingEntry']), ProductionProjects::labUsers()->pluck('id')->all(), $order->expected_delivery_date ?? today());
                            if ($task->status === ProductionTaskStatus::Completed) {
                                ProductionProjects::syncTaskCompleted($task->refresh()->load('projectTask.project'));
                            }
                        }
                    }
                }
            }

            static::refreshOrderCompletion($order);
        });
    }

    protected static function pieces(OrderLine $line): int
    {
        $quantity = (float) $line->quantity;
        $pieces = (int) round($quantity);

        if ($pieces < 1 || abs($quantity - $pieces) > 0.00001) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_whole_pieces', ['product' => $line->product->name]));
        }

        return $pieces;
    }

    private static function assertEditable(ManufacturingEntry $entry): void
    {
        if ($entry->order->state !== OrderState::Confirmed) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_only_confirmed'));
        }
        if ($entry->order->manufacturing_managed_at) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_already_managed'));
        }
    }

    private static function releaseCurrentUnit(ManufacturingEntry $entry): void
    {
        if (! $entry->product_unit_id) {
            return;
        }

        ProductUnit::query()->whereKey($entry->product_unit_id)->update([
            'status'        => UnitStatus::InLab->value,
            'status_since'  => today(),
            'order_id'      => null,
            'order_line_id' => null,
        ]);
        $entry->update(['product_unit_id' => null]);
    }

    private static function removeProductionChoice(ManufacturingEntry $entry): void
    {
        $production = $entry->productionTask;
        if (! $production) {
            return;
        }
        if ($production->status === ProductionTaskStatus::Completed) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_completed_choice'));
        }

        ProductionProjects::syncTaskCancelled($production->load('projectTask.project'));
        $production->projectTask?->delete();
        $production->delete();
        $entry->unsetRelation('productionTask');
    }
}
