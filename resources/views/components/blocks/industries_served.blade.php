@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
@endphp

<section class="saas-block">
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

    @if(count($items) > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $item)
                @if(!empty($item['title']))
                    @php $features = is_array($item['features'] ?? null) ? $item['features'] : []; @endphp
                    <div class="saas-shell rounded-2xl p-6 transition-all duration-200 hover:scale-[1.01]"
                         style="background: var(--sn-surface-strong);">
                        @if(!empty($item['icon']))
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl"
                                 style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                                    class="h-5 w-5" style="color: var(--sn-primary);" />
                            </div>
                        @endif
                        <h3 class="text-base font-semibold" style="color: var(--sn-text);">{{ $item['title'] }}</h3>
                        @if(!empty($item['description']))
                            <p class="mt-2 text-sm leading-relaxed" style="color: var(--sn-muted);">{{ $item['description'] }}</p>
                        @endif
                        @if(count($features) > 0)
                            <ul class="mt-3 space-y-1.5">
                                @foreach($features as $feat)
                                    @if(!empty($feat['text']))
                                        <li class="flex items-center gap-2 text-xs" style="color: var(--sn-muted);">
                                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                 style="color: var(--sn-primary);">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            {{ $feat['text'] }}
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @if(!empty($data['cta']['label']) && !empty($data['cta']['link']['url']))
        <div class="mt-8 text-center">
            <a href="{{ $data['cta']['link']['url'] }}"
               class="saas-btn-primary inline-flex items-center gap-2 rounded-xl px-7 py-3 text-sm font-semibold transition-all duration-200">
                {{ $data['cta']['label'] }}
            </a>
        </div>
    @endif
</section>
