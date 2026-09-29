<?php

namespace Huvant\Calendar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;

class Attendee extends Model
{
    protected $table = 'huvant_calendar_attendees';

    protected $fillable = ['event_id', 'user_id', 'response'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
