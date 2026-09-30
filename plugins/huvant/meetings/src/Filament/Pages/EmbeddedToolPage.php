<?php

namespace Huvant\Meetings\Filament\Pages;

use Filament\Pages\Page;
use Huvant\Meetings\Support\MeetingsSso;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;

/**
 * Minutes, Canvas and Milo inside the ERP: the ERP top bar (menu, search,
 * notifications, timer, profile) stays on top and the tool fills the page below.
 * "p" is the tool's own path, kept in the address bar so links and reloads land
 * where the user was.
 */
abstract class EmbeddedToolPage extends Page
{
    /** The tool's first screen, under /riunioni. */
    public const DEFAULT_PATH = MeetingsSso::HOME;

    protected string $view = 'huvant-meetings::filament.pages.embedded-tool';

    #[Url(as: 'p')]
    public string $path = '';

    public static function tool(): string
    {
        return 'minutes';
    }

    /** Which tool a /riunioni path belongs to: the page that shows it. */
    public static function toolFor(string $path): string
    {
        $route = '/'.ltrim(substr($path, strlen(rtrim(MeetingsSso::HOME, '/'))), '/');
        $route = strtok($route, '?') ?: '/';

        return match (true) {
            str_starts_with($route, '/milo')                                    => 'milo',
            str_starts_with($route, '/canvas'), str_starts_with($route, '/projects'),
            str_starts_with($route, '/admin/projects'), str_starts_with($route, '/admin/voice-profiles') => 'canvas',
            default                                                                                      => 'minutes',
        };
    }

    /** Only paths of the tool itself may be framed. */
    public static function safePath(?string $path): ?string
    {
        $home = rtrim(MeetingsSso::HOME, '/');
        if (! is_string($path) || $path === '' || str_contains($path, '\\') || str_contains($path, '//')
            || ! ($path === $home || str_starts_with($path, $home.'/') || str_starts_with($path, $home.'?'))) {
            return null;
        }

        return $path;
    }

    public function getFrameSrc(): string
    {
        $path = static::safePath($this->path);

        return $path && static::toolFor($path) === static::tool() ? $path : static::DEFAULT_PATH;
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    /** @return array<string, string> */
    public static function toolUrls(): array
    {
        return [
            'minutes' => MinutesPage::getUrl(),
            'canvas'  => CanvasPage::getUrl(),
            'milo'    => MiloPage::getUrl(),
        ];
    }
}
