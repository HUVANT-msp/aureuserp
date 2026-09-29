<?php

namespace Huvant\Bridge\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'name'          => $this->name,
            'work_email'    => $this->work_email,
            'job_title'     => $this->job_title,
            'department_id' => $this->department_id,
            'company_id'    => $this->company_id,
            'partner_id'    => $this->partner_id,
            'is_active'     => (bool) $this->is_active,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
