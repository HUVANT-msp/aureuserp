<?php

namespace Huvant\Teams\Support;

use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

/**
 * Who may see which projects: members of the teams attached to a project.
 * Administrators and the integration account see everything; a project with
 * no team is visible to administrators only.
 */
class ProjectTeams
{
    public const PIVOT = 'huvant_project_teams';

    /** Granted to accounts that must see every project (e.g. the Minutes integration). */
    public const VIEW_ALL_PERMISSION = 'huvant_view_all_projects';

    /** @var array<int, array{bypass: bool, projects: list<int>}> per-request cache */
    private static array $cache = [];

    public static function bypasses(User $user): bool
    {
        return static::resolve($user)['bypass'];
    }

    /** @return list<int> */
    public static function visibleProjectIds(User $user): array
    {
        return static::resolve($user)['projects'];
    }

    public static function forget(): void
    {
        static::$cache = [];
    }

    /** @param  list<int>  $teamIds */
    public static function syncProjectTeams(int $projectId, array $teamIds): void
    {
        $teamIds = array_values(array_unique(array_map('intval', $teamIds)));
        DB::transaction(function () use ($projectId, $teamIds): void {
            DB::table(self::PIVOT)->where('project_id', $projectId)->whereNotIn('team_id', $teamIds ?: [0])->delete();
            $existing = DB::table(self::PIVOT)->where('project_id', $projectId)->pluck('team_id')->all();
            $now = now();
            foreach (array_diff($teamIds, $existing) as $teamId) {
                DB::table(self::PIVOT)->insert([
                    'project_id' => $projectId, 'team_id' => $teamId, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        });
        static::forget();
    }

    /** @return array{bypass: bool, projects: list<int>} */
    private static function resolve(User $user): array
    {
        return static::$cache[$user->getKey()] ??= [
            'bypass'   => static::computeBypass($user),
            'projects' => DB::table(self::PIVOT)
                ->join('user_team', 'user_team.team_id', '=', self::PIVOT.'.team_id')
                ->where('user_team.user_id', $user->getKey())
                ->distinct()
                ->pluck(self::PIVOT.'.project_id')
                ->map(fn ($id): int => (int) $id)
                ->all(),
        ];
    }

    private static function computeBypass(User $user): bool
    {
        if ($user->roles()->get()->contains(fn (Role $role): bool => $role->isSystemRole())) {
            return true;
        }

        return $user->checkPermissionTo(self::VIEW_ALL_PERMISSION, 'web');
    }
}
