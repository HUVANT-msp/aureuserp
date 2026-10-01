<?php

namespace Huvant\Insights\Console;

use Huvant\Insights\Support\NewEmployee;
use Illuminate\Console\Command;
use Webkul\Security\Models\User;

/** Send the "set your password" invitation to colleagues already added. */
class SendInvites extends Command
{
    protected $signature = 'huvant:invite {emails* : e-mail addresses of the people to invite}';

    protected $description = 'E-mail the invitation to set a password to the given colleagues';

    public function handle(): int
    {
        $failed = 0;
        foreach ($this->argument('emails') as $email) {
            $user = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($email))])->first();
            if (! $user) {
                $this->error("No user with e-mail {$email}");
                $failed++;

                continue;
            }
            if (NewEmployee::invite($user, null, notify: false)) {
                $this->info("Invited {$user->name} <{$user->email}>");
            } else {
                $this->error("Could not e-mail {$user->email}");
                $failed++;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
