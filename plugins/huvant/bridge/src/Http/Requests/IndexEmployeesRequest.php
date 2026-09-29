<?php

namespace Huvant\Bridge\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Webkul\Employee\Models\Employee;

class IndexEmployeesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Employee::class);
    }

    public function rules(): array
    {
        return [
            'user_id'       => ['nullable', 'integer', 'exists:users,id'],
            'work_email'    => ['nullable', 'email'],
            'is_active'     => ['nullable', 'boolean'],
            'updated_after' => ['nullable', 'date'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
