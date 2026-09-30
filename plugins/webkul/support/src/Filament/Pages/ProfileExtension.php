<?php

namespace Webkul\Support\Filament\Pages;

use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Adds a section to the user's own profile (Profile::$extensions): its fields,
 * their current values, and what saving them does.
 */
interface ProfileExtension
{
    /** @return array<Component> */
    public static function components(Authenticatable $user): array;

    /** @return array<string, mixed> values for the extension's fields */
    public static function fill(Authenticatable $user): array;

    /** @param array<string, mixed> $data the whole profile form state */
    public static function save(Authenticatable $user, array $data): void;
}
