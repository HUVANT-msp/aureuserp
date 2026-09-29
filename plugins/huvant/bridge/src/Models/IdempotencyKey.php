<?php

namespace Huvant\Bridge\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    protected $table = 'huvant_bridge_idempotency_keys';

    protected $fillable = [
        'user_id',
        'key',
        'request_hash',
        'state',
        'response_status',
        'response_body',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'      => 'datetime',
            'response_status' => 'integer',
        ];
    }
}
