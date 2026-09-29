<?php

namespace Huvant\Documents\Support;

use Huvant\Documents\Models\Document;
use Huvant\Documents\Models\Folder;
use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
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

    public static function directory(?int $projectId): string
    {
        return 'huvant-documents/'.($projectId ?: 'tasks');
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

        return $document->project_id !== null && Project::query()->whereKey($document->project_id)->exists();
    }

    public static function canManage(User $user, Document|Folder $item): bool
    {
        return (int) $item->creator_id === (int) $user->getKey() || static::isAdmin($user);
    }

    public static function createFolder(User $user, int $projectId, ?int $parentId, string $name): Folder
    {
        $name = static::cleanName($name);
        if ($parentId && ! Folder::query()->whereKey($parentId)->where('project_id', $projectId)->exists()) {
            throw new RuntimeException('La cartella di destinazione non esiste più.');
        }
        if (static::siblingFolders($projectId, $parentId)->contains(fn (Folder $f): bool => Str::lower($f->name) === Str::lower($name))) {
            throw new RuntimeException("Esiste già una cartella «{$name}» qui.");
        }

        return Folder::query()->create([
            'project_id' => $projectId, 'parent_id' => $parentId, 'name' => $name, 'creator_id' => $user->getKey(),
        ]);
    }

    public static function renameFolder(Folder $folder, string $name): void
    {
        $name = static::cleanName($name);
        $clash = static::siblingFolders($folder->project_id, $folder->parent_id)
            ->contains(fn (Folder $f): bool => $f->isNot($folder) && Str::lower($f->name) === Str::lower($name));
        if ($clash) {
            throw new RuntimeException("Esiste già una cartella «{$name}» qui.");
        }
        $folder->update(['name' => $name]);
    }

    public static function deleteFolder(Folder $folder): void
    {
        if ($folder->children()->exists() || $folder->documents()->exists()) {
            throw new RuntimeException('La cartella non è vuota: sposta o elimina prima il suo contenuto.');
        }
        $folder->delete();
    }

    /** A file already stored on the documents disk (e.g. by an upload field). */
    public static function registerStoredFile(User $user, ?int $projectId, ?int $folderId, ?int $taskId, string $path, ?string $originalName = null): Document
    {
        $disk = Storage::disk(self::DISK);
        $name = static::cleanName($originalName ?: basename($path), 255);

        return Document::query()->create([
            'project_id'    => $projectId,
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

    public static function storeUpload(User $user, ?int $projectId, ?int $folderId, ?int $taskId, UploadedFile $file): Document
    {
        $path = $file->store(static::directory($projectId), self::DISK);

        return static::registerStoredFile($user, $projectId, $folderId, $taskId, $path, $file->getClientOriginalName());
    }

    public static function createNote(User $user, ?int $projectId, ?int $folderId, ?int $taskId, string $title, ?string $body): Document
    {
        return Document::query()->create([
            'project_id' => $projectId,
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
        if ($folderId && ! Folder::query()->whereKey($folderId)->where('project_id', $document->project_id)->exists()) {
            throw new RuntimeException('La cartella di destinazione non esiste più.');
        }
        $document->update(['folder_id' => $folderId]);
    }

    /** @return array<int, string> folder id => "Cartella / Sottocartella", for pickers */
    public static function folderOptions(int $projectId): array
    {
        $folders = Folder::query()->where('project_id', $projectId)->get(['id', 'parent_id', 'name'])->keyBy('id');
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
                return ($unit === 'B' ? $bytes : number_format($bytes, $bytes < 10 ? 1 : 0, ',', '.')).' '.$unit;
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

    private static function siblingFolders(?int $projectId, ?int $parentId): Collection
    {
        return Folder::query()->where('project_id', $projectId)
            ->when($parentId, fn ($q) => $q->where('parent_id', $parentId), fn ($q) => $q->whereNull('parent_id'))
            ->get(['id', 'name']);
    }

    private static function cleanName(string $name, int $max = 160): string
    {
        $name = trim(preg_replace('/[\\\\\/\x00-\x1F]+/u', ' ', $name) ?? '');
        if ($name === '') {
            throw new RuntimeException('Il nome non può essere vuoto.');
        }

        return Str::limit($name, $max, '');
    }
}
