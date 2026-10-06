<?php

namespace Huvant\Documents\Support;

use Huvant\Documents\Models\Document;
use Huvant\Documents\Models\Folder;
use Illuminate\Database\Eloquent\Builder;

/** Where documents and folders live: a project, or a recipe of the lab. */
final class DocumentSpace
{
    private function __construct(public readonly string $column, public readonly int $id) {}

    public static function project(int $projectId): self
    {
        return new self('project_id', $projectId);
    }

    public static function recipe(int $recipeId): self
    {
        return new self('recipe_id', $recipeId);
    }

    public static function of(Document|Folder $item): ?self
    {
        return match (true) {
            $item->recipe_id !== null  => self::recipe((int) $item->recipe_id),
            $item->project_id !== null => self::project((int) $item->project_id),
            default                    => null,
        };
    }

    /** @return array{project_id: int|null, recipe_id: int|null} */
    public function attributes(): array
    {
        return ['project_id' => $this->column === 'project_id' ? $this->id : null, 'recipe_id' => $this->column === 'recipe_id' ? $this->id : null];
    }

    public function scope(Builder $query): Builder
    {
        return $query->where($this->column, $this->id);
    }

    public function directory(): string
    {
        return 'huvant-documents/'.($this->column === 'recipe_id' ? 'recipes/' : '').$this->id;
    }

    public function isRecipe(): bool
    {
        return $this->column === 'recipe_id';
    }
}
