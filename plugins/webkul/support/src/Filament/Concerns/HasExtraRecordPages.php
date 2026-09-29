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

    /** @var list<class-string> built-in record pages another plugin took out of the tabs (routes stay) */
    protected static array $hiddenRecordPages = [];

    public static function hideRecordPage(string $page): void
    {
        static::$hiddenRecordPages[] = $page;
    }

    /** @param  list<class-string>  $pages */
    protected static function visibleRecordPages(array $pages): array
    {
        return array_values(array_filter([...$pages, ...static::extraRecordPageClasses()], fn (string $page): bool => ! in_array($page, static::$hiddenRecordPages, true)));
    }

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
