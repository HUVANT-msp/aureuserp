<?php

namespace Huvant\Bridge\Observers;

use DateTimeInterface;
use Huvant\Bridge\Jobs\SendBridgeWebhook;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;

class BridgeWebhookObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Model $model): void
    {
        $this->dispatch($model, 'created', $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->dispatch($model, 'updated', $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->dispatch($model, 'deleted', []);
    }

    public function restored(Model $model): void
    {
        $this->dispatch($model, 'updated', $model->getChanges());
    }

    /** @param array<string, mixed> $changes */
    private function dispatch(Model $model, string $event, array $changes): void
    {
        if (! config('huvant-bridge.webhook.url') || ! config('huvant-bridge.webhook.secret')) {
            return;
        }

        $type = match (true) {
            $model instanceof Task    => 'task',
            $model instanceof Project => 'project',
            $model instanceof User    => 'user',
            $model instanceof Partner => 'partner',
            default                   => null,
        };

        if ($type === null) {
            return;
        }

        $allowed = match ($type) {
            'task'    => ['title', 'description', 'state', 'stage_id', 'project_id', 'partner_id', 'parent_id', 'deadline', 'progress', 'priority', 'is_active', 'updated_at', 'deleted_at'],
            'project' => ['name', 'description', 'stage_id', 'partner_id', 'user_id', 'is_active', 'updated_at', 'deleted_at'],
            'user'    => ['name', 'email', 'is_active', 'default_company_id', 'updated_at', 'deleted_at'],
            'partner' => ['name', 'email', 'phone', 'mobile', 'is_active', 'company_id', 'updated_at', 'deleted_at'],
        };

        $updatedAt = $model->getAttribute('updated_at');

        SendBridgeWebhook::dispatch([
            'event_id'       => (string) Str::uuid(),
            'resource_type'  => $type,
            'erp_id'         => $model->getKey(),
            'event_type'     => $event,
            'updated_at'     => $updatedAt instanceof DateTimeInterface ? $updatedAt->format(DATE_ATOM) : now()->toISOString(),
            'changed_fields' => array_keys(Arr::only($changes, $allowed)),
        ])->onQueue((string) config('huvant-bridge.webhook.queue', 'default'));
    }
}
