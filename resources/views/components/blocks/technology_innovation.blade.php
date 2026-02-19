@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
    $imageUrl = $data['image_media_url'] ?? null;
@endphp

<section class="saas-block">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-start">
        <div>
            @if(!empty($data['subtitle']))
                <div class="mb-2 text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-primary);">
                    {{ $data['subtitle'] }}
                </div>
            @endif
            @if(!empty($data['title']))
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl" style="color: var(--sn-text);">
                    {{ $data['title'] }}
                </h2>
            @endif
            @if(!empty($data['description']))
                <p class="mt-4 text-sm leading-relaxed sm:text-base" style="color: var(--sn-muted);">
                    {{ $data['description'] }}
                </p>
            @endif

            @if(count($items) > 0)
                <div class="mt-8 space-y-4">
                    @foreach($items as $item)
                        @if(!empty($item['title']))
                            <div class="flex gap-4">
                                @if(!empty($item['icon']))
                                    <div class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl"
                                         style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                        <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                                            class="h-5 w-5" style="color: var(--sn-primary);" />
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-sm font-semibold" style="color: var(--sn-text);">{{ $item['title'] }}</h3>
                                    @if(!empty($item['description']))
                                        <p class="mt-1 text-sm leading-relaxed" style="color: var(--sn-muted);">
                                            {{ $item['description'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if(!empty($data['cta']['label']) && !empty($data['cta']['link']['url']))
                <div class="mt-8">
                    <a href="{{ $data['cta']['link']['url'] }}"
                       class="saas-btn-primary inline-flex items-center gap-2 rounded-xl px-7 py-3 text-sm font-semibold transition-all duration-200">
                        {{ $data['cta']['label'] }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        @if($imageUrl)
            <div class="saas-shell overflow-hidden rounded-2xl lg:sticky lg:top-24" style="aspect-ratio: 4/3;">
                <img src="{{ $imageUrl }}" alt="{{ $data['title'] ?? '' }}" class="h-full w-full object-cover">
            </div>
        @endif
    </div>
</section>
