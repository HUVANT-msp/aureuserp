<?php

namespace Huvant\Documents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

class Folder extends Model
{
    protected $table = 'huvant_document_folders';

    protected $fillable = ['project_id', 'recipe_id', 'parent_id', 'name', 'creator_id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /** @return list<Folder> from the project root down to this folder */
    public function trail(): array
    {
        $trail = [];
        $folder = $this;
        while ($folder && count($trail) < 20) {
            array_unshift($trail, $folder);
            $folder = $folder->parent;
        }

        return $trail;
    }
}
