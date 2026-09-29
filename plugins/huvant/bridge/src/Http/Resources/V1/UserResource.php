<?php

namespace Huvant\Bridge\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'email'               => $this->email,
            'language'            => $this->language,
            'is_active'           => (bool) $this->is_active,
            'default_company_id'  => $this->default_company_id,
            'resource_permission' => $this->resource_permission?->value,
            'partner_id'          => $this->partner_id,
            'allowed_company_ids' => $this->whenLoaded('allowedCompanies', fn () => $this->allowedCompanies->modelKeys()),
            'role_ids'            => $this->whenLoaded('roles', fn () => $this->roles->modelKeys()),
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
