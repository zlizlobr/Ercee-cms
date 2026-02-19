@php
    $stats = is_array($data['stats'] ?? null) ? $data['stats'] : [];
    $cols = count($stats) <= 2 ? 'sm:grid-cols-2' : (count($stats) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4');
@endphp

<section class="saas-shell overflow-hidden rounded-2xl py-12" style="background: var(--sn-surface-strong);">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        @if(!empty($data['subtitle']) || !empty($data['title']))
            <div class="mb-10 text-center">
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
            </div>
        @endif

        @if(count($stats) > 0)
            <div class="grid grid-cols-2 gap-8 text-center {{ $cols }}">
                @foreach($stats as $stat)
                    @if(!empty($stat['value']) && !empty($stat['label']))
                        <div>
                            @if(!empty($stat['icon']))
                                <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-xl"
                                     style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                    <x-dynamic-component :component="'heroicon-o-' . $stat['icon']"
                                        class="h-5 w-5" style="color: var(--sn-primary);" />
                                </div>
                            @endif
                            <div class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color: var(--sn-primary);">
                                {{ $stat['value'] }}
                            </div>
                            <div class="mt-1.5 text-sm font-medium" style="color: var(--sn-text);">{{ $stat['label'] }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
