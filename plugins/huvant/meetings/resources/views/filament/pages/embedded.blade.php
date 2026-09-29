<x-filament-panels::page>
    @if ($frameUrl)
        <div style="display:flex;justify-content:flex-end;margin-top:-0.5rem">
            <a href="{{ $directUrl }}" target="_blank" rel="noopener"
               style="font-size:0.8125rem;font-weight:600;color:rgb(var(--primary-600))">
                Apri a schermo intero ↗
            </a>
        </div>
        <iframe
            src="{{ $frameUrl }}"
            title="{{ $this->getTitle() }}"
            allow="microphone; clipboard-read; clipboard-write; fullscreen"
            style="width:100%;height:calc(100vh - 11rem);min-height:32rem;border:0;border-radius:0.75rem;background:#fff;box-shadow:0 0 0 1px rgba(0,0,0,0.08)"
        ></iframe>
    @else
        <div style="padding:1.5rem;border-radius:0.75rem;box-shadow:0 0 0 1px rgba(0,0,0,0.08)">
            Il collegamento a Huvant Meeting Minutes non è configurato:
            imposta <code>HUVANT_MEETINGS_URL</code> e <code>HUVANT_MEETINGS_SSO_SECRET</code>.
        </div>
    @endif
</x-filament-panels::page>
