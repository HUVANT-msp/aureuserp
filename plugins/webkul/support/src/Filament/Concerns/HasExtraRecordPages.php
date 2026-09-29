<?php

namespace Webkul\Support\Filament\Concerns;

/**
 * Lets other plugins add record pages (a tab in the record sub-navigation and
 * its route) without editing the resource. Register during service provider
 * registration, before the panel builds its routes.
 */
trait HasExtraRecordPages
{
    /** @var array<string, array{page: class-string, path: string}> */
    protected static array $extraRecordPages = [];

    public static function registerRecordPage(string $key, string $page, string $path): void
    {
        static::$extraRecordPages[$key] = ['page' => $page, 'path' => $path];
    }

    /** @return list<class-string> */
    protected static function extraRecordPageClasses(): array
    {
        return array_values(array_map(fn (array $entry): string => $entry['page'], static::$extraRecordPages));
    }

    protected static function extraRecordPageRoutes(): array
    {
        return array_map(fn (array $entry) => $entry['page']::route($entry['path']), static::$extraRecordPages);
    }
}
