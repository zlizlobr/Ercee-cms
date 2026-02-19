@php
    $services = is_array($data['services'] ?? null) ? $data['services'] : [];
    $cols = count($services) <= 2 ? 'sm:grid-cols-2' : (count($services) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-3');
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

    @if(count($services) > 0)
        <div class="grid grid-cols-1 gap-4 {{ $cols }}">
            @foreach($services as $service)
                @if(!empty($service['title']))
                    <div class="saas-shell group rounded-2xl p-6 transition-all duration-200 hover:scale-[1.01]"
                         style="background: var(--sn-surface-strong);">
                        @if(!empty($service['icon']))
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl"
                                 style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                <x-dynamic-component :component="'heroicon-o-' . $service['icon']"
                                    class="h-5 w-5" style="color: var(--sn-primary);" />
                            </div>
                        @endif
                        <h3 class="text-base font-semibold" style="color: var(--sn-text);">
                            {{ $service['title'] }}
                        </h3>
                        @if(!empty($service['description']))
                            <p class="mt-2 text-sm leading-relaxed" style="color: var(--sn-muted);">
                                {{ $service['description'] }}
                            </p>
                        @endif
                        @if(!empty($service['link']['url']))
                            <a href="{{ $service['link']['url'] }}"
                               class="mt-4 inline-flex items-center gap-1 text-xs font-semibold transition-colors duration-200"
                               style="color: var(--sn-primary);">
                                {{ $service['link']['label'] ?? __('Learn more') }}
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
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
