@php
    $bgImageUrl = $data['background_image_url'] ?? null;
@endphp

<section class="saas-shell relative overflow-hidden rounded-2xl">
    @if($bgImageUrl)
        <div class="absolute inset-0">
            <img src="{{ $bgImageUrl }}" alt="" class="h-full w-full object-cover opacity-20">
            <div class="absolute inset-0" style="background: linear-gradient(120deg, var(--sn-bg) 45%, transparent);"></div>
        </div>
    @endif

    <div class="relative grid grid-cols-1 gap-10 px-6 py-12 sm:px-10 lg:grid-cols-2 lg:items-center lg:gap-16 lg:py-16">
        <div>
            @if(!empty($data['subtitle']))
                <div class="mb-3 text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-primary);">
                    {{ $data['subtitle'] }}
                </div>
            @endif
            @if(!empty($data['title']))
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl md:text-4xl" style="color: var(--sn-text);">
                    {{ $data['title'] }}
                </h2>
            @endif
            @if(!empty($data['description']))
                <p class="mt-4 text-sm leading-relaxed sm:text-base" style="color: var(--sn-muted);">
                    {{ $data['description'] }}
                </p>
            @endif

            @if(!empty($data['primary']['label']) || !empty($data['secondary']['label']))
                <div class="mt-8 flex flex-wrap items-center gap-4">
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
        </div>

        @if($bgImageUrl)
            <div class="hidden overflow-hidden rounded-2xl lg:block" style="aspect-ratio: 4/3;">
                <img src="{{ $bgImageUrl }}" alt="{{ $data['title'] ?? '' }}" class="h-full w-full object-cover">
            </div>
        @endif
    </div>
</section>
