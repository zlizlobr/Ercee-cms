@php
    $steps = is_array($data['steps'] ?? null) ? $data['steps'] : [];
@endphp

<section class="saas-block">
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
        @if(!empty($data['description']))
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed sm:text-base" style="color: var(--sn-muted);">
                {{ $data['description'] }}
            </p>
        @endif
    </div>

    @if(count($steps) > 0)
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($steps as $idx => $step)
                @if(!empty($step['title']))
                    <div class="saas-shell relative rounded-2xl p-6" style="background: var(--sn-surface-strong);">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl text-sm font-bold"
                                 style="background: color-mix(in srgb, var(--sn-primary) 18%, transparent); color: var(--sn-primary); border: 1px solid color-mix(in srgb, var(--sn-primary) 30%, transparent);">
                                {{ $step['step'] ?? ($idx + 1) }}
                            </div>
                            @if(!empty($step['icon']))
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl"
                                     style="background: color-mix(in srgb, var(--sn-primary) 10%, transparent);">
                                    <x-dynamic-component :component="'heroicon-o-' . $step['icon']"
                                        class="h-5 w-5" style="color: var(--sn-primary);" />
                                </div>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold" style="color: var(--sn-text);">{{ $step['title'] }}</h3>
                        @if(!empty($step['description']))
                            <p class="mt-2 text-sm leading-relaxed" style="color: var(--sn-muted);">
                                {{ $step['description'] }}
                            </p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
