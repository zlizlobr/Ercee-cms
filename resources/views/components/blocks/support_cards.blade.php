@php
    $cards = is_array($data['cards'] ?? null) ? $data['cards'] : [];
    $cols = count($cards) <= 2 ? 'sm:grid-cols-2' : (count($cards) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4');
@endphp

<section class="saas-block">
    <div class="mb-8 text-center">
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
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed sm:text-base" style="color: var(--sn-muted);">
                {{ $data['description'] }}
            </p>
        @endif
    </div>

    @if(count($cards) > 0)
        <div class="grid grid-cols-1 gap-4 {{ $cols }}">
            @foreach($cards as $card)
                @if(!empty($card['title']))
                    <div class="saas-shell flex flex-col rounded-2xl p-6 transition-all duration-200 hover:scale-[1.01]"
                         style="background: var(--sn-surface-strong);">
                        @if(!empty($card['icon']))
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl"
                                 style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                <x-dynamic-component :component="'heroicon-o-' . $card['icon']"
                                    class="h-5 w-5" style="color: var(--sn-primary);" />
                            </div>
                        @endif
                        <h3 class="text-base font-semibold" style="color: var(--sn-text);">{{ $card['title'] }}</h3>
                        @if(!empty($card['description']))
                            <p class="mt-2 flex-1 text-sm leading-relaxed" style="color: var(--sn-muted);">
                                {{ $card['description'] }}
                            </p>
                        @endif
                        @if(!empty($card['link_label']) && !empty($card['link']['url']))
                            <a href="{{ $card['link']['url'] }}"
                               class="mt-4 inline-flex items-center gap-1 text-xs font-semibold transition-colors duration-200"
                               style="color: var(--sn-primary);">
                                {{ $card['link_label'] }}
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
</section>
