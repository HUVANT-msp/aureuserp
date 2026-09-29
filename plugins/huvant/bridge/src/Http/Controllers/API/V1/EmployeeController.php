<?php

namespace Huvant\Bridge\Http\Controllers\API\V1;

use Huvant\Bridge\Http\Requests\IndexEmployeesRequest;
use Huvant\Bridge\Http\Resources\V1\EmployeeResource;
use Huvant\Bridge\Support\BridgeAuthorization;
use Illuminate\Support\Facades\Gate;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Scopes\CompanyScope;

class EmployeeController
{
    public function index(IndexEmployeesRequest $request)
    {
        Gate::authorize('viewAny', Employee::class);

        $data = $request->validated();
        $query = Employee::query();

        if (! BridgeAuthorization::isSuperAdmin($request->user())) {
            $query->withoutGlobalScope(CompanyScope::class)
                ->whereIn('employees_employees.company_id', BridgeAuthorization::companyIds($request->user()));
        }

        $query->when($data['user_id'] ?? null, fn ($query, $id) => $query->where('user_id', $id));
        $query->when($data['work_email'] ?? null, fn ($query, $email) => $query->where('work_email', $email));
        $query->when(array_key_exists('is_active', $data), fn ($query) => $query->where('is_active', $data['is_active']));
        $query->when($data['updated_after'] ?? null, fn ($query, $date) => $query->where('updated_at', '>', $date));

        return EmployeeResource::collection($query->orderBy('id')->paginate($data['per_page'] ?? 25));
    }
}
