<?php

namespace Huvant\Home\Console;

use Huvant\Home\Support\Home;
use Illuminate\Console\Command;
use Webkul\Security\Models\User;

class MiloBriefings extends Command
{
    protected $signature = 'huvant:milo-briefings {slot=manual : morning, midday or manual} {--user= : one user id or email}';

    protected $description = "Ask Milo for each person's dashboard brief.";

    public function handle(): int
    {
        $slot = in_array($this->argument('slot'), ['morning', 'midday', 'manual'], true) ? $this->argument('slot') : 'manual';
        $only = $this->option('user');
        $people = $only
            ? User::query()->where(fn ($q) => $q->where('id', $only)->orWhere('email', $only))->get()
            : Home::recipients();

        $ok = 0;
        foreach ($people as $person) {
            $brief = Home::generate($person, $slot);
            $brief && ! $brief->error ? $ok++ : $this->warn("No brief for {$person->email}");
        }
        $this->info("Milo briefs: {$ok}/{$people->count()}");

        return self::SUCCESS;
    }
}
