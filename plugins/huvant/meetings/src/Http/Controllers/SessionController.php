<?php

namespace Huvant\Meetings\Http\Controllers;

use Filament\Facades\Filament;
use Huvant\Meetings\Support\MeetingsSso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SessionController
{
    /** Minutes asks for a session: sign one for the user already logged in here. */
    public function sso(Request $request): RedirectResponse
    {
        $next = (string) $request->query('next', MeetingsSso::HOME);
        if (! str_starts_with($next, '/') || str_starts_with($next, '//') || str_contains($next, '\\')) {
            $next = MeetingsSso::HOME;
        }

        return redirect()->away(MeetingsSso::url($request->user()->email, $next));
    }

    /** The ERP app launcher (groups, icons, permissions) for Minutes and Canvas. */
    public function navigation(): JsonResponse
    {
        $groups = [];
        foreach (Filament::getNavigation() as $group) {
            $items = collect($group->getItems())
                ->filter(fn ($item) => $item->isVisible())
                ->map(fn ($item) => ['label' => (string) $item->getLabel(), 'url' => (string) $item->getUrl()])
                ->filter(fn (array $item) => $item['url'] !== '')
                ->values();
            $label = $group->getLabel();
            $icon = $group->getIcon();
            if (! $label || ! $icon || $items->isEmpty()) {
                continue;
            }
            $groups[] = [
                'label' => (string) $label,
                'icon'  => svg(is_string($icon) ? $icon : $icon->value)->toHtml(),
                'url'   => $items->first()['url'],
                'items' => $items->all(),
            ];
        }

        return response()->json(['groups' => $groups])->header('Cache-Control', 'private, max-age=300');
    }

    /** Leaving from Minutes leaves the ERP as well (the Logout listener clears Minutes). */
    public function logout(Request $request): RedirectResponse
    {
        Filament::auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(Filament::getLoginUrl());
    }
}
