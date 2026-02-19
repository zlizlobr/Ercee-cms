@php
    $cards = is_array($data['cards'] ?? null) ? $data['cards'] : [];
    $cols = count($cards) <= 2 ? 'sm:grid-cols-2' : (count($cards) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4');
    $ctaBgUrl = $data['cta_background_image_url'] ?? null;
@endphp

<section class="space-y-4">
    <div class="saas-block">
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
                        <div class="saas-shell rounded-2xl p-6" style="background: var(--sn-surface-strong);">
                            @if(!empty($card['icon']))
                                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl"
                                     style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                    <x-dynamic-component :component="'heroicon-o-' . $card['icon']"
                                        class="h-5 w-5" style="color: var(--sn-primary);" />
                                </div>
                            @endif
                            <h3 class="text-base font-semibold" style="color: var(--sn-text);">{{ $card['title'] }}</h3>
                            @if(!empty($card['description']))
                                <p class="mt-2 text-sm leading-relaxed" style="color: var(--sn-muted);">
                                    {{ $card['description'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @if(!empty($data['cta_title']))
        <div class="saas-shell relative overflow-hidden rounded-2xl px-6 py-10 text-center sm:px-12">
            @if($ctaBgUrl)
                <div class="absolute inset-0">
                    <img src="{{ $ctaBgUrl }}" alt="" class="h-full w-full object-cover opacity-10">
                </div>
            @endif
            <div class="relative">
                <h3 class="text-xl font-bold sm:text-2xl" style="color: var(--sn-text);">{{ $data['cta_title'] }}</h3>
                @if(!empty($data['cta_description']))
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed" style="color: var(--sn-muted);">
                        {{ $data['cta_description'] }}
                    </p>
                @endif
                @if(!empty($data['cta_button']['label']) && !empty($data['cta_button']['link']['url']))
                    <a href="{{ $data['cta_button']['link']['url'] }}"
                       class="saas-btn-primary mt-6 inline-flex items-center gap-2 rounded-xl px-7 py-3 text-sm font-semibold transition-all duration-200">
                        {{ $data['cta_button']['label'] }}
                    </a>
                @endif
            </div>
        </div>
    @endif
</section>
