<?php

namespace Huvant\Teams\Scopes;

use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;

/**
 * Projects, tasks, milestones and timesheets are visible only when the
 * project belongs to one of the user's teams (see ProjectTeams).
 */
class TeamProjectScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        if (! $user instanceof User || ProjectTeams::bypasses($user)) {
            return; // queues, console and administrators are not filtered
        }

        $table = $model->getTable();
        $projects = ProjectTeams::visibleProjectIds($user) ?: [0];

        if ($model instanceof Project) {
            $builder->whereIn("{$table}.id", $projects);

            return;
        }

        if ($model instanceof Task) {
            $builder->where(function (Builder $query) use ($table, $projects, $user): void {
                $query->whereIn("{$table}.project_id", $projects)
                    // A task outside any project stays with its creator and assignees.
                    ->orWhere(function (Builder $query) use ($table, $user): void {
                        $query->whereNull("{$table}.project_id")
                            ->where(function (Builder $query) use ($table, $user): void {
                                $query->where("{$table}.creator_id", $user->getKey())
                                    ->orWhereExists(function ($exists) use ($table, $user): void {
                                        $exists->selectRaw('1')
                                            ->from('projects_task_users')
                                            ->whereColumn('projects_task_users.task_id', "{$table}.id")
                                            ->where('projects_task_users.user_id', $user->getKey());
                                    });
                            });
                    });
            });

            return;
        }

        // Milestones and timesheet lines follow their project; people keep their own hours.
        $builder->where(function (Builder $query) use ($table, $projects, $user): void {
            $query->whereIn("{$table}.project_id", $projects);
            if ($table === 'analytic_records') {
                $query->orWhere("{$table}.user_id", $user->getKey());
            }
        });
    }
}
