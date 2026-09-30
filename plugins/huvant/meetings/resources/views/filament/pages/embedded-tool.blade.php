{{-- The tool fills the page under the ERP top bar. It tells us where it is
     (to keep the address bar in step) and asks us to go when a link leaves it. --}}
<div
    class="hv-embed"
    wire:ignore
    x-data="{
        tool: @js($this::tool()),
        urls: @js($this::toolUrls()),
        fit() {
            const bar = document.querySelector('.fi-topbar-ctn') || document.querySelector('.fi-topbar');
            this.$el.style.setProperty('--hv-embed-top', Math.max(0, Math.round(bar ? bar.getBoundingClientRect().bottom : 64)) + 'px');
        },
        receive(event) {
            if (event.origin !== window.location.origin || event.source !== this.$refs.frame.contentWindow) return;
            const data = event.data || {};
            if (data.type === 'huvant:leave' && typeof data.href === 'string' && data.href.startsWith('/') && ! data.href.startsWith('//')) {
                window.location.assign(data.href);
                return;
            }
            if (data.type !== 'huvant:route' || typeof data.path !== 'string' || ! data.path.startsWith('/riunioni')) return;
            if (data.tool && data.tool !== this.tool && this.urls[data.tool]) {
                window.location.assign(this.urls[data.tool] + '?p=' + encodeURIComponent(data.path));
                return;
            }
            const url = new URL(window.location.href);
            url.searchParams.set('p', data.path);
            window.history.replaceState(window.history.state, '', url);
        },
    }"
    x-init="
        fit();
        const bar = document.querySelector('.fi-topbar-ctn');
        if (bar && window.ResizeObserver) new ResizeObserver(() => fit()).observe(bar);
        window.addEventListener('resize', () => fit());
        window.addEventListener('message', (event) => receive(event));
    "
>
    <iframe
        x-ref="frame"
        src="{{ $this->getFrameSrc() }}"
        title="{{ $this->getTitle() }}"
        allow="microphone; autoplay; clipboard-read; clipboard-write; fullscreen"
    ></iframe>
</div>
