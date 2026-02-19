@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $bgImageUrl = $data['background_image_url_large'] ?? $data['background_image_url'] ?? null;
    $ctaPrimary = $data['cta_primary'] ?? null;
    $ctaSecondary = $data['cta_secondary'] ?? null;
    $stats = is_array($data['stats'] ?? null) ? $data['stats'] : [];
    $badges = is_array($data['badges'] ?? null) ? $data['badges'] : [];

    if (!$bgImageUrl && !empty($data['background_image'])) {
        $bgImageUrl = asset('storage/' . $data['background_image']);
    }
@endphp

<section class="saas-hero relative overflow-hidden rounded-2xl py-20 sm:py-28">
    @if($bgImageUrl)
        <div class="absolute inset-0 overflow-hidden rounded-2xl">
            <img src="{{ $bgImageUrl }}" alt="" class="h-full w-full object-cover opacity-15">
        </div>
    @endif

    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        @if(!empty($data['subtitle']))
            <div class="mb-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest"
                 style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent); color: var(--sn-primary); border: 1px solid color-mix(in srgb, var(--sn-primary) 30%, transparent);">
                <span class="inline-block h-1.5 w-1.5 rounded-full" style="background: var(--sn-primary); box-shadow: 0 0 8px var(--sn-glow);"></span>
                {{ $data['subtitle'] }}
            </div>
        @endif

        @if(!empty($data['title']))
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl" style="color: var(--sn-text);">
                {{ $data['title'] }}
            </h1>
        @endif

        @if(!empty($data['description']))
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed" style="color: var(--sn-muted);">
                {{ $data['description'] }}
            </p>
        @endif

        @if(!empty($ctaPrimary['label']) || !empty($ctaSecondary['label']))
            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                @if(!empty($ctaPrimary['label']) && !empty($ctaPrimary['url']))
                    <a href="{{ $ctaPrimary['url'] }}"
                       class="saas-btn-primary inline-flex items-center gap-2 rounded-xl px-8 py-3.5 text-base font-semibold transition-all duration-200">
                        {{ $ctaPrimary['label'] }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                @endif
                @if(!empty($ctaSecondary['label']) && !empty($ctaSecondary['url']))
                    <a href="{{ $ctaSecondary['url'] }}"
                       class="saas-btn-secondary inline-flex items-center gap-2 rounded-xl px-8 py-3.5 text-base font-semibold transition-all duration-200">
                        {{ $ctaSecondary['label'] }}
                    </a>
                @endif
            </div>
        @endif

        @if(count($badges) > 0)
            <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
                @foreach($badges as $badge)
                    @if(!empty($badge['text']))
                        <span class="saas-shell inline-block rounded-full px-3 py-1 text-xs font-medium" style="color: var(--sn-muted);">
                            {{ $badge['text'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @if(count($stats) > 0)
        <div class="relative mx-auto mt-14 max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="saas-shell grid grid-cols-2 gap-px overflow-hidden rounded-2xl sm:grid-cols-4">
                @foreach($stats as $stat)
                    @if(!empty($stat['value']) && !empty($stat['label']))
                        <div class="px-6 py-5 text-center" style="background: var(--sn-surface);">
                            <div class="text-2xl font-bold" style="color: var(--sn-primary);">{{ $stat['value'] }}</div>
                            <div class="mt-1 text-xs font-medium uppercase tracking-wide" style="color: var(--sn-muted);">{{ $stat['label'] }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</section>
