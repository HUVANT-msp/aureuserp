<?php

namespace Huvant\Bridge\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Webkul\Security\Models\User;

class IndexUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', User::class);
    }

    public function rules(): array
    {
        return [
            'email'         => ['nullable', 'email'],
            'is_active'     => ['nullable', 'boolean'],
            'updated_after' => ['nullable', 'date'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
