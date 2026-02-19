@php
    $bgImageUrl = $data['background_image_url'] ?? null;
    $badges = is_array($data['badges'] ?? null) ? $data['badges'] : [];
    $stats = is_array($data['stats'] ?? null) ? $data['stats'] : [];
@endphp

<section class="saas-hero relative overflow-hidden rounded-2xl py-14 sm:py-20">
    @if($bgImageUrl)
        <div class="absolute inset-0 overflow-hidden rounded-2xl">
            <img src="{{ $bgImageUrl }}" alt="" class="h-full w-full object-cover opacity-10">
        </div>
    @endif

    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        @if(!empty($data['subtitle']))
            <div class="mb-3 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest"
                 style="background: color-mix(in srgb, var(--sn-primary) 12%, transparent); color: var(--sn-primary); border: 1px solid color-mix(in srgb, var(--sn-primary) 28%, transparent);">
                {{ $data['subtitle'] }}
            </div>
        @endif

        @if(!empty($data['title']))
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl md:text-5xl" style="color: var(--sn-text);">
                {{ $data['title'] }}
            </h1>
        @endif

        @if(!empty($data['description']))
            <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed sm:text-lg" style="color: var(--sn-muted);">
                {{ $data['description'] }}
            </p>
        @endif

        @if(!empty($data['primary']['label']) || !empty($data['secondary']['label']))
            <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                @if(!empty($data['primary']['label']) && !empty($data['primary']['link']['url']))
                    <a href="{{ $data['primary']['link']['url'] }}"
                       class="saas-btn-primary inline-flex items-center gap-2 rounded-xl px-7 py-3 text-sm font-semibold transition-all duration-200">
                        {{ $data['primary']['label'] }}
                    </a>
                @endif
                @if(!empty($data['secondary']['label']) && !empty($data['secondary']['link']['url']))
                    <a href="{{ $data['secondary']['link']['url'] }}"
                       class="saas-btn-secondary inline-flex items-center gap-2 rounded-xl px-7 py-3 text-sm font-semibold transition-all duration-200">
                        {{ $data['secondary']['label'] }}
                    </a>
                @endif
            </div>
        @endif

        @if(count($badges) > 0)
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
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
        <div class="relative mx-auto mt-10 max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="saas-shell grid grid-cols-2 gap-px overflow-hidden rounded-2xl sm:grid-cols-4">
                @foreach($stats as $stat)
                    @if(!empty($stat['value']) && !empty($stat['label']))
                        <div class="px-5 py-4 text-center" style="background: var(--sn-surface);">
                            <div class="text-xl font-bold" style="color: var(--sn-primary);">{{ $stat['value'] }}</div>
                            <div class="mt-0.5 text-xs font-medium uppercase tracking-wide" style="color: var(--sn-muted);">{{ $stat['label'] }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</section>
