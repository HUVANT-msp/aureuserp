<?php

namespace Huvant\Calendar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

class Event extends Model
{
    protected $table = 'huvant_calendar_events';

    protected $fillable = ['kind', 'title', 'description', 'location', 'starts_at', 'ends_at', 'all_day', 'private', 'organizer_id', 'project_id'];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'all_day' => 'boolean', 'private' => 'boolean'];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'huvant_calendar_attendees', 'event_id', 'user_id')->withPivot('response');
    }
}
