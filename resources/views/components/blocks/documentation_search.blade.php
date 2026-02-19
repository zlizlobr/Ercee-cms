@php
    $quickLinks = is_array($data['quick_links'] ?? null) ? $data['quick_links'] : [];
    $placeholder = $data['placeholder'] ?? __('Search documentation...');
    $buttonLabel = $data['button_label'] ?? __('Search');
@endphp

<section class="saas-hero overflow-hidden rounded-2xl py-12 sm:py-16">
    <div class="mx-auto max-w-2xl px-4 text-center sm:px-6">
        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl" style="color: var(--sn-text);">
            {{ $data['title'] ?? __('Documentation') }}
        </h2>
        @if(!empty($data['subtitle']))
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed" style="color: var(--sn-muted);">
                {{ $data['subtitle'] }}
            </p>
        @endif

        <form class="mt-8 flex gap-2" role="search" action="#" method="get">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--sn-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" name="q" placeholder="{{ $placeholder }}"
                       class="saas-shell w-full rounded-xl py-3 pl-10 pr-4 text-sm outline-none transition-all duration-200"
                       style="background: var(--sn-surface-strong); color: var(--sn-text);">
            </div>
            <button type="submit"
                    class="saas-btn-primary flex-shrink-0 rounded-xl px-6 py-3 text-sm font-semibold transition-all duration-200">
                {{ $buttonLabel }}
            </button>
        </form>

        @if(count($quickLinks) > 0)
            <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                <span class="text-xs" style="color: var(--sn-muted);">Quick links:</span>
                @foreach($quickLinks as $link)
                    @if(!empty($link['label']) && !empty($link['anchor']))
                        <a href="#{{ $link['anchor'] }}"
                           class="saas-shell rounded-full px-3 py-1 text-xs font-medium transition-all duration-200 hover:scale-[1.03]"
                           style="color: var(--sn-primary);">
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
