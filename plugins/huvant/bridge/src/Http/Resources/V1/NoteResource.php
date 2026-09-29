<?php

namespace Huvant\Bridge\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'type'             => $this->type,
            'subject'          => $this->subject,
            'body'             => $this->body,
            'is_internal'      => (bool) $this->is_internal,
            'messageable_type' => $this->messageable_type,
            'messageable_id'   => $this->messageable_id,
            'causer_id'        => $this->causer_id,
            'created_at'       => $this->created_at,
        ];
    }
}
