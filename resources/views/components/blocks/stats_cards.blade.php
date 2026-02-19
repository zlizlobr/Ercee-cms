@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
    $cols = count($items) <= 2 ? 'sm:grid-cols-2' : (count($items) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4');
@endphp

<section class="saas-block">
    @if(!empty($data['subtitle']) || !empty($data['title']))
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
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed" style="color: var(--sn-muted);">
                    {{ $data['description'] }}
                </p>
            @endif
        </div>
    @endif

    @if(count($items) > 0)
        <div class="grid grid-cols-1 gap-4 {{ $cols }}">
            @foreach($items as $item)
                @if(!empty($item['value']) && !empty($item['label']))
                    <div class="saas-shell flex flex-col items-center gap-3 rounded-2xl p-6 text-center transition-all duration-200 hover:scale-[1.02]"
                         style="background: var(--sn-surface-strong);">
                        @if(!empty($item['icon']))
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl"
                                 style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                                    class="h-5 w-5" style="color: var(--sn-primary);" />
                            </div>
                        @endif
                        <div class="text-3xl font-extrabold tracking-tight" style="color: var(--sn-primary);">
                            {{ $item['value'] }}
                        </div>
                        <div class="text-sm font-medium" style="color: var(--sn-text);">
                            {{ $item['label'] }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
