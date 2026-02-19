@php
    $steps = is_array($data['steps'] ?? null) ? $data['steps'] : [];
    $benefits = is_array($data['benefits'] ?? null) ? $data['benefits'] : [];
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
        <div class="relative space-y-6">
            @foreach($steps as $idx => $step)
                @if(!empty($step['title']))
                    @php $imageUrl = $step['image_media_url'] ?? null; @endphp
                    <div class="saas-shell grid grid-cols-1 gap-6 rounded-2xl p-6 sm:p-8 lg:grid-cols-2 lg:items-center"
                         style="background: var(--sn-surface-strong);">
                        <div class="flex gap-5">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-sm font-bold"
                                 style="background: color-mix(in srgb, var(--sn-primary) 18%, transparent); color: var(--sn-primary); border: 1px solid color-mix(in srgb, var(--sn-primary) 30%, transparent);">
                                {{ $step['number'] ?? ($idx + 1) }}
                            </div>
                            <div>
                                <h3 class="text-base font-bold" style="color: var(--sn-text);">{{ $step['title'] }}</h3>
                                @if(!empty($step['description']))
                                    <p class="mt-2 text-sm leading-relaxed" style="color: var(--sn-muted);">
                                        {{ $step['description'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($imageUrl)
                            <div class="overflow-hidden rounded-xl lg:order-first" style="aspect-ratio: 16/9;">
                                <img src="{{ $imageUrl }}" alt="{{ $step['title'] }}" class="h-full w-full object-cover">
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @if(count($benefits) > 0)
        @php
            $benCols = count($benefits) <= 2 ? 'sm:grid-cols-2' : (count($benefits) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4');
        @endphp
        <div class="mt-8 grid grid-cols-1 gap-4 {{ $benCols }}">
            @foreach($benefits as $benefit)
                @if(!empty($benefit['title']))
                    <div class="saas-shell rounded-2xl p-5" style="background: var(--sn-surface);">
                        @if(!empty($benefit['icon']))
                            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl"
                                 style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                <x-dynamic-component :component="'heroicon-o-' . $benefit['icon']"
                                    class="h-4 w-4" style="color: var(--sn-primary);" />
                            </div>
                        @endif
                        <h4 class="text-sm font-semibold" style="color: var(--sn-text);">{{ $benefit['title'] }}</h4>
                        @if(!empty($benefit['description']))
                            <p class="mt-1 text-xs leading-relaxed" style="color: var(--sn-muted);">{{ $benefit['description'] }}</p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
