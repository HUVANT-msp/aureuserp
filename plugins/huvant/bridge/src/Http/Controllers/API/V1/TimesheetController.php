<?php

namespace Huvant\Bridge\Http\Controllers\API\V1;

use Huvant\Bridge\Http\Requests\IndexTimesheetsRequest;
use Huvant\Bridge\Http\Resources\V1\TimesheetResource;
use Huvant\Bridge\Support\BridgeAuthorization;
use Illuminate\Support\Facades\Gate;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Timesheet\Models\Timesheet;

class TimesheetController
{
    public function index(IndexTimesheetsRequest $request)
    {
        Gate::authorize('viewAny', Timesheet::class);

        $data = $request->validated();
        $query = Timesheet::query();

        if (! BridgeAuthorization::isSuperAdmin($request->user())) {
            $query->withoutGlobalScope(CompanyScope::class)
                ->whereIn('analytic_records.company_id', BridgeAuthorization::companyIds($request->user()));
        }

        foreach (['project_id', 'task_id', 'user_id'] as $field) {
            $query->when($data[$field] ?? null, fn ($query, $value) => $query->where($field, $value));
        }

        $query->when($data['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date));
        $query->when($data['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date));
        $query->when($data['updated_after'] ?? null, fn ($query, $date) => $query->where('updated_at', '>', $date));

        return TimesheetResource::collection($query->orderBy('id')->paginate($data['per_page'] ?? 25));
    }
}
