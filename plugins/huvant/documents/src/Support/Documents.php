<?php

namespace Huvant\Documents\Support;

use Huvant\Documents\Models\Document;
use Huvant\Documents\Models\Folder;
use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

/**
 * Who sees a document is who sees its project (the project's teams) or, for a
 * task's document, who sees the task. Anyone who sees it can add and edit
 * notes; renaming, moving and deleting are for its author and administrators.
 */
class Documents
{
    public const DISK = 'local';

    public const MAX_UPLOAD_KB = 100 * 1024;

    public static function directory(int|DocumentSpace|null $space): string
    {
        $space = static::space($space);

        return $space ? $space->directory() : 'huvant-documents/tasks';
    }

    /** A project id (as the project pages pass it) or a space; null for a task outside any project. */
    public static function space(int|DocumentSpace|null $space): ?DocumentSpace
    {
        return is_int($space) ? DocumentSpace::project($space) : $space;
    }

    public static function isAdmin(User $user): bool
    {
        if (class_exists(ProjectTeams::class)) {
            return ProjectTeams::bypasses($user);
        }

        return $user->roles()->get()->contains(fn (Role $role): bool => $role->isSystemRole());
    }

    /** Relies on the signed-in user: project and task queries carry the team scope. */
    public static function canView(Document $document): bool
    {
        if ($document->task_id) {
            return Task::query()->whereKey($document->task_id)->exists();
        }

        // A recipe's documents are for everyone who works in the lab inventory.
        if ($document->recipe_id) {
            return DB::table('products_products')->where('id', $document->recipe_id)->whereNull('deleted_at')->exists();
        }

        return $document->project_id !== null && Project::query()->whereKey($document->project_id)->exists();
    }

    public static function canManage(User $user, Document|Folder $item): bool
    {
        return (int) $item->creator_id === (int) $user->getKey() || static::isAdmin($user);
    }

    public static function createFolder(User $user, int|DocumentSpace $space, ?int $parentId, string $name): Folder
    {
        $space = static::space($space);
        $name = static::cleanName($name);
        if ($parentId && ! $space->scope(Folder::query()->whereKey($parentId))->exists()) {
            throw new RuntimeException('The destination folder no longer exists.');
        }
        if (static::siblingFolders($space, $parentId)->contains(fn (Folder $f): bool => Str::lower($f->name) === Str::lower($name))) {
            throw new RuntimeException("A folder named \"{$name}\" already exists here.");
        }

        return Folder::query()->create($space->attributes() + [
            'parent_id' => $parentId, 'name' => $name, 'creator_id' => $user->getKey(),
        ]);
    }

    public static function renameFolder(Folder $folder, string $name): void
    {
        $name = static::cleanName($name);
        $clash = static::siblingFolders(DocumentSpace::of($folder), $folder->parent_id)
            ->contains(fn (Folder $f): bool => $f->isNot($folder) && Str::lower($f->name) === Str::lower($name));
        if ($clash) {
            throw new RuntimeException("A folder named \"{$name}\" already exists here.");
        }
        $folder->update(['name' => $name]);
    }

    public static function deleteFolder(Folder $folder): void
    {
        if ($folder->children()->exists() || $folder->documents()->exists()) {
            throw new RuntimeException('The folder is not empty: move or delete its contents first.');
        }
        $folder->delete();
    }

    /** A file already stored on the documents disk (e.g. by an upload field). */
    public static function registerStoredFile(User $user, int|DocumentSpace|null $space, ?int $folderId, ?int $taskId, string $path, ?string $originalName = null): Document
    {
        $disk = Storage::disk(self::DISK);
        $name = static::cleanName($originalName ?: basename($path), 255);

        return Document::query()->create(static::spaceAttributes($space) + [
            'folder_id'     => $taskId ? null : $folderId,
            'task_id'       => $taskId,
            'type'          => Document::FILE,
            'title'         => $name,
            'disk'          => self::DISK,
            'path'          => $path,
            'original_name' => $name,
            'mime_type'     => $disk->mimeType($path) ?: 'application/octet-stream',
            'size'          => $disk->size($path),
            'creator_id'    => $user->getKey(),
            'updated_by'    => $user->getKey(),
        ]);
    }

    public static function storeUpload(User $user, int|DocumentSpace|null $space, ?int $folderId, ?int $taskId, UploadedFile $file): Document
    {
        $path = $file->store(static::directory($space), self::DISK);

        return static::registerStoredFile($user, $space, $folderId, $taskId, $path, $file->getClientOriginalName());
    }

    public static function createNote(User $user, int|DocumentSpace|null $space, ?int $folderId, ?int $taskId, string $title, ?string $body): Document
    {
        return Document::query()->create(static::spaceAttributes($space) + [
            'folder_id'  => $taskId ? null : $folderId,
            'task_id'    => $taskId,
            'type'       => Document::NOTE,
            'title'      => static::cleanName($title, 255),
            'body'       => $body,
            'creator_id' => $user->getKey(),
            'updated_by' => $user->getKey(),
        ]);
    }

    public static function move(Document $document, ?int $folderId): void
    {
        $space = DocumentSpace::of($document);
        if ($folderId && (! $space || ! $space->scope(Folder::query()->whereKey($folderId))->exists())) {
            throw new RuntimeException('The destination folder no longer exists.');
        }
        $document->update(['folder_id' => $folderId]);
    }

    /** @return array<int, string> folder id => "Folder / Subfolder", for pickers */
    public static function folderOptions(int|DocumentSpace $space): array
    {
        $folders = static::space($space)->scope(Folder::query())->get(['id', 'parent_id', 'name'])->keyBy('id');
        $label = function (Folder $folder) use ($folders): string {
            $parts = [$folder->name];
            $seen = [$folder->getKey() => true];
            while ($folder->parent_id && ($folder = $folders->get($folder->parent_id)) && ! isset($seen[$folder->getKey()])) {
                $seen[$folder->getKey()] = true;
                array_unshift($parts, $folder->name);
            }

            return implode(' / ', $parts);
        };

        return $folders->map($label)->sort(SORT_NATURAL | SORT_FLAG_CASE)->all();
    }

    public static function humanSize(?int $bytes): string
    {
        if ($bytes === null) {
            return '';
        }
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024 || $unit === 'GB') {
                return ($unit === 'B' ? $bytes : number_format($bytes, $bytes < 10 ? 1 : 0, '.', ',')).' '.$unit;
            }
            $bytes /= 1024;
        }

        return '';
    }

    public static function counts(int $projectId): array
    {
        return [
            'documents' => Document::query()->where('project_id', $projectId)->whereNull('task_id')->count(),
            'task'      => Document::query()->where('project_id', $projectId)->whereNotNull('task_id')->count(),
        ];
    }

    /** @return array{project_id: int|null, recipe_id: int|null} */
    private static function spaceAttributes(int|DocumentSpace|null $space): array
    {
        return static::space($space)?->attributes() ?? ['project_id' => null, 'recipe_id' => null];
    }

    private static function siblingFolders(?DocumentSpace $space, ?int $parentId): Collection
    {
        return Folder::query()->where($space?->column ?? 'project_id', $space?->id)
            ->when($parentId, fn ($q) => $q->where('parent_id', $parentId), fn ($q) => $q->whereNull('parent_id'))
            ->get(['id', 'name']);
    }

    private static function cleanName(string $name, int $max = 160): string
    {
        $name = trim(preg_replace('/[\\\\\/\x00-\x1F]+/u', ' ', $name) ?? '');
        if ($name === '') {
            throw new RuntimeException('The name cannot be empty.');
        }

        return Str::limit($name, $max, '');
    }
}
