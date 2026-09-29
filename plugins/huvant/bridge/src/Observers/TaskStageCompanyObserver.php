<?php

namespace Huvant\Bridge\Observers;

use Webkul\Project\Models\Project;
use Webkul\Project\Models\TaskStage;
use Webkul\Support\Models\Scopes\CompanyScope;

class TaskStageCompanyObserver
{
    public function saving(TaskStage $taskStage): void
    {
        $taskStage->company_id = Project::withoutGlobalScope(CompanyScope::class)
            ->find($taskStage->project_id)?->company_id;
    }
}
