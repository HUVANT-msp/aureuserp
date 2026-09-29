<?php

namespace Huvant\Bridge\Http\Requests;

use Huvant\Bridge\Support\BridgeAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', User::class);
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'language'              => ['nullable', 'string', 'max:10'],
            'is_active'             => ['sometimes', 'boolean'],
            'default_company_id'    => ['required', 'integer', 'exists:companies,id'],
            'allowed_company_ids'   => ['required', 'array', 'min:1'],
            'allowed_company_ids.*' => ['integer', 'exists:companies,id'],
            'resource_permission'   => ['sometimes', Rule::enum(PermissionType::class)],
            'role_ids'              => ['sometimes', 'array'],
            'role_ids.*'            => ['integer', 'exists:roles,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! in_array((int) $this->integer('default_company_id'), array_map('intval', $this->input('allowed_company_ids', [])), true)) {
                $validator->errors()->add('default_company_id', 'The default company must be included in allowed_company_ids.');
            }

            BridgeAuthorization::validateUserAssignments($validator, $this->user(), $this->only([
                'allowed_company_ids', 'resource_permission', 'role_ids',
            ]));
        });
    }
}
