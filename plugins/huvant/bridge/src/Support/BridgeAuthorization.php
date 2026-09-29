<?php

namespace Huvant\Bridge\Support;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

class BridgeAuthorization
{
    public static function validateUserAssignments(Validator $validator, User $actor, array $data): void
    {
        $isSuperAdmin = static::isSuperAdmin($actor);

        if (array_key_exists('role_ids', $data) && ! $isSuperAdmin) {
            $validator->errors()->add('role_ids', 'Only a super administrator may assign roles.');
        }

        if (array_key_exists('resource_permission', $data) && ! $isSuperAdmin) {
            $validator->errors()->add('resource_permission', 'Only a super administrator may change resource access.');
        }

        if ($isSuperAdmin || ! array_key_exists('allowed_company_ids', $data)) {
            return;
        }

        $actorCompanyIds = static::companyIds($actor);
        $forbiddenCompanyIds = collect($data['allowed_company_ids'])
            ->map(fn ($id): int => (int) $id)
            ->diff($actorCompanyIds);

        if ($forbiddenCompanyIds->isNotEmpty()) {
            $validator->errors()->add('allowed_company_ids', 'You may only assign companies available to your account.');
        }
    }

    public static function isSuperAdmin(User $user): bool
    {
        return $user->roles()->get()->contains(
            fn (Role $role): bool => $role->isSystemRole(),
        );
    }

    public static function companyIds(User $user): Collection
    {
        return DB::table('user_allowed_companies')
            ->where('user_id', $user->getKey())
            ->pluck('company_id')
            ->push($user->default_company_id)
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();
    }
}
