<?php

namespace Huvant\Documents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;

class Document extends Model
{
    public const FILE = 'file';

    public const NOTE = 'note';

    protected $table = 'huvant_documents';

    protected $fillable = [
        'project_id', 'folder_id', 'task_id', 'type', 'title', 'body',
        'disk', 'path', 'original_name', 'mime_type', 'size', 'creator_id', 'updated_by',
    ];

    protected $casts = ['size' => 'integer'];

    protected static function booted(): void
    {
        // The stored file goes with its document.
        static::deleted(function (Document $document): void {
            if ($document->isFile() && $document->path) {
                Storage::disk($document->disk ?: 'local')->delete($document->path);
            }
        });
    }

    public function isFile(): bool
    {
        return $this->type === self::FILE;
    }

    public function isNote(): bool
    {
        return $this->type === self::NOTE;
    }

    /** Images and PDFs open in the browser; everything else downloads. */
    public function previewable(): bool
    {
        return $this->isFile() && (str_starts_with((string) $this->mime_type, 'image/') || $this->mime_type === 'application/pdf');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
