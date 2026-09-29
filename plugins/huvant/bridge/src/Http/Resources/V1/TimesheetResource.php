<?php

namespace Huvant\Bridge\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimesheetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'date'       => $this->date,
            'hours'      => (float) $this->unit_amount,
            'amount'     => (float) $this->amount,
            'user_id'    => $this->user_id,
            'partner_id' => $this->partner_id,
            'project_id' => $this->project_id,
            'task_id'    => $this->task_id,
            'company_id' => $this->company_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
