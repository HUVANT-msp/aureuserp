<?php

namespace Huvant\Tasks\Support;

/** Project and people colours from the Huvant brand stripe, stable per id. */
class Palette
{
    public const PROJECT = ['#0075de', '#e30d7f', '#19b2b2', '#ee7113', '#884393', '#474798', '#d8141d', '#0f9d58', '#b7791f', '#5b6b7f'];

    public static function project(?int $id, ?string $color = null): string
    {
        if ($color && preg_match('/^#[0-9a-f]{6}$/i', $color)) {
            return $color;
        }

        return $id ? self::PROJECT[($id - 1) % count(self::PROJECT)] : '#8b8782';
    }

    public static function person(int $id): string
    {
        return self::PROJECT[($id * 7 + 3) % count(self::PROJECT)];
    }

    public static function initials(?string $name): string
    {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        $letters = array_map(fn (string $p): string => mb_strtoupper(mb_substr($p, 0, 1)), array_slice(array_filter($parts), 0, 2));

        return implode('', $letters) ?: '?';
    }
}
