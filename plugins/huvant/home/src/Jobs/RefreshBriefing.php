<?php

namespace Huvant\Home\Jobs;

use Huvant\Home\Support\Home;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Webkul\Security\Models\User;

/** "Refresh" on the home page: one brief, in the background. */
class RefreshBriefing implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $timeout = 150;

    public function __construct(public int $userId) {}

    public function uniqueId(): string
    {
        return (string) $this->userId;
    }

    public function handle(): void
    {
        if ($user = User::query()->find($this->userId)) {
            Home::generate($user, 'manual');
        }
    }
}
