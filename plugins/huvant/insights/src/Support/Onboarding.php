<?php

namespace Huvant\Insights\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Models\Tag;
use Webkul\Security\Models\User;

/**
 * One person, one record everywhere: every user of the ERP is an employee and
 * an internal contact ("Team Huvant"), whichever way the user was added.
 */
class Onboarding
{
    public const TEAM_TAG = 'Team Huvant';

    /** Roles that are services, not people. */
    public const SERVICE_ROLES = ['Huvant Integration'];

    public static function isPerson(User $user): bool
    {
        return ! $user->roles()->whereIn('name', self::SERVICE_ROLES)->exists();
    }

    /** The user's employee and contact, created or linked; safe to call again. */
    public static function onboard(User $user): ?Employee
    {
        if (! $user->partner_id || ! static::isPerson($user)) {
            return null;
        }

        return DB::transaction(function () use ($user): Employee {
            $employee = Employee::withoutGlobalScopes()->where('user_id', $user->getKey())->whereNull('deleted_at')->first()
                ?? static::unlinkedMatch($user);

            if ($employee) {
                $previousCard = $employee->partner_id;
                $employee->forceFill([
                    'user_id'    => $user->getKey(),
                    'partner_id' => $user->partner_id,
                    'name'       => $user->name,
                    'work_email' => $user->email,
                    'is_active'  => true,
                ])->saveQuietly();
                static::retireCard($previousCard, $user->partner_id);
            } else {
                $employee = Employee::withoutEvents(fn (): Employee => Employee::withoutGlobalScopes()->create([
                    'user_id'     => $user->getKey(),
                    'partner_id'  => $user->partner_id,
                    'name'        => $user->name,
                    'work_email'  => $user->email,
                    'company_id'  => $user->default_company_id,
                    'calendar_id' => static::usualCalendar(),
                    'creator_id'  => auth()->id() ?? $user->getKey(),
                    'is_active'   => true,
                ]));
            }

            $card = Partner::withoutGlobalScopes()->find($user->partner_id);
            if ($card) {
                $tag = Tag::query()->firstOrCreate(['name' => self::TEAM_TAG]);
                $card->tags()->syncWithoutDetaching([$tag->getKey()]);
                // Contacts made earlier by hand for the same person are the same person.
                Partner::withoutGlobalScopes()
                    ->whereKeyNot($card->getKey())->whereNull('deleted_at')->whereNull('user_id')->where('account_type', 'individual')
                    ->whereRaw('LOWER(name) = ?', [Str::lower($user->name)])
                    ->where(fn ($q) => $q->whereNull('email')->orWhereRaw('LOWER(email) = ?', [Str::lower($user->email)]))
                    ->get()->each(fn (Partner $duplicate) => static::retireCard($duplicate->getKey(), $card->getKey()));
            }

            return $employee;
        });
    }

    /** An employee added before the user (same e-mail, or same name and no e-mail). */
    private static function unlinkedMatch(User $user): ?Employee
    {
        return Employee::withoutGlobalScopes()->whereNull('user_id')->whereNull('deleted_at')
            ->where(fn ($q) => $q->whereRaw('LOWER(work_email) = ?', [Str::lower($user->email)])
                ->orWhere(fn ($q) => $q->whereNull('work_email')->whereRaw('LOWER(name) = ?', [Str::lower($user->name)])))
            ->orderBy('id')->first();
    }

    /** A contact replaced by the user's own: archived (restorable), when nothing else uses it. */
    private static function retireCard(?int $partnerId, int $keep): void
    {
        if (! $partnerId || $partnerId === $keep) {
            return;
        }
        $card = Partner::withoutGlobalScopes()->whereNull('deleted_at')->find($partnerId);
        if (! $card || $card->user_id
            || Employee::withoutGlobalScopes()->where('partner_id', $partnerId)->whereNull('deleted_at')->exists()
            || DB::table('partners_partners')->where('parent_id', $partnerId)->whereNull('deleted_at')->exists()) {
            return;
        }
        $card->delete();
    }

    private static function usualCalendar(): ?int
    {
        $id = DB::table('employees_employees')->whereNull('deleted_at')->whereNotNull('calendar_id')
            ->select('calendar_id', DB::raw('count(*) as uses'))->groupBy('calendar_id')->orderByDesc('uses')->value('calendar_id');

        return $id ? (int) $id : null;
    }
}
