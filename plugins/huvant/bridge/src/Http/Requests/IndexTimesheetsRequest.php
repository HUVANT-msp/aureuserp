<?php

namespace Huvant\Bridge\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Webkul\Timesheet\Models\Timesheet;

class IndexTimesheetsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Timesheet::class);
    }

    public function rules(): array
    {
        return [
            'project_id'    => ['nullable', 'integer', 'exists:projects_projects,id'],
            'task_id'       => ['nullable', 'integer', 'exists:projects_tasks,id'],
            'user_id'       => ['nullable', 'integer', 'exists:users,id'],
            'date_from'     => ['nullable', 'date'],
            'date_to'       => ['nullable', 'date', 'after_or_equal:date_from'],
            'updated_after' => ['nullable', 'date'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
