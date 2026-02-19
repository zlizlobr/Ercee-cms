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
            @foreach($items as $idx => $item)
                @if(!empty($item['title']))
                    <div class="saas-shell flex gap-4 rounded-2xl p-5" style="background: var(--sn-surface-strong);">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl"
                             style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                            @if(!empty($item['icon']))
                                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                                    class="h-5 w-5" style="color: var(--sn-primary);" />
                            @else
                                <span class="text-sm font-bold" style="color: var(--sn-primary);">{{ $idx + 1 }}</span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold" style="color: var(--sn-text);">{{ $item['title'] }}</h3>
                            @if(!empty($item['description']))
                                <p class="mt-1 text-xs leading-relaxed" style="color: var(--sn-muted);">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
