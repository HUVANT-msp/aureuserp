<?php

namespace Huvant\Bridge\Http\Requests;

use Huvant\Bridge\Support\BridgeAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Webkul\Security\Enums\PermissionType;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actor = $this->user();
        $user = $this->route('user');

        if (! Gate::allows('update', $user)) {
            return false;
        }

        return BridgeAuthorization::isSuperAdmin($actor)
            || BridgeAuthorization::companyIds($actor)->contains((int) $user->default_company_id);
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name'                  => ['sometimes', 'required', 'string', 'max:255'],
            'email'                 => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password'              => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
            'language'              => ['nullable', 'string', 'max:10'],
            'is_active'             => ['sometimes', 'boolean'],
            'default_company_id'    => ['sometimes', 'required', 'integer', 'exists:companies,id'],
            'allowed_company_ids'   => ['sometimes', 'required', 'array', 'min:1'],
            'allowed_company_ids.*' => ['integer', 'exists:companies,id'],
            'resource_permission'   => ['sometimes', Rule::enum(PermissionType::class)],
            'role_ids'              => ['sometimes', 'array'],
            'role_ids.*'            => ['integer', 'exists:roles,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $user = $this->route('user');
            $defaultCompanyId = $this->has('default_company_id')
                ? $this->integer('default_company_id')
                : $user->default_company_id;
            $allowedCompanyIds = $this->has('allowed_company_ids')
                ? $this->input('allowed_company_ids', [])
                : BridgeAuthorization::companyIds($user)->all();

            if (! in_array((int) $defaultCompanyId, array_map('intval', $allowedCompanyIds), true)) {
                $validator->errors()->add('default_company_id', 'The default company must be included in allowed_company_ids.');
            }

            BridgeAuthorization::validateUserAssignments($validator, $this->user(), [
                ...$this->only(['resource_permission', 'role_ids']),
                'allowed_company_ids' => $allowedCompanyIds,
            ]);
        });
    }
}
