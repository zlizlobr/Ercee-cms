@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
@endphp

<section class="saas-block">
    @if(!empty($data['title']) || !empty($data['subtitle']))
        <div class="mb-8">
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
                <p class="mt-3 max-w-2xl text-sm leading-relaxed sm:text-base" style="color: var(--sn-muted);">
                    {{ $data['description'] }}
                </p>
            @endif
        </div>
    @endif

    @if(count($items) > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $item)
                <div class="saas-shell group overflow-hidden rounded-2xl" style="background: var(--sn-surface-strong);">
                    @if(!empty($item['image_media_url']))
                        <div class="overflow-hidden" style="aspect-ratio: 4/3;">
                            <img src="{{ $item['image_media_url'] }}" alt="{{ $item['title'] ?? '' }}"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                        </div>
                    @endif
                    @if(!empty($item['title']) || !empty($item['description']))
                        <div class="p-4">
                            @if(!empty($item['title']))
                                <h3 class="text-sm font-semibold" style="color: var(--sn-text);">{{ $item['title'] }}</h3>
                            @endif
                            @if(!empty($item['description']))
                                <p class="mt-1 text-xs leading-relaxed" style="color: var(--sn-muted);">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty($data['cta']['label']) && !empty($data['cta']['link']['url']))
        <div class="mt-8 text-center">
            <a href="{{ $data['cta']['link']['url'] }}"
               class="saas-btn-secondary inline-flex items-center gap-2 rounded-xl px-7 py-3 text-sm font-semibold transition-all duration-200">
                {{ $data['cta']['label'] }}
            </a>
        </div>
    @endif
</section>
