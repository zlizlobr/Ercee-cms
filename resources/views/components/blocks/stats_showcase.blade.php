@php
    $stats = is_array($data['stats'] ?? null) ? $data['stats'] : [];
    $logos = is_array($data['logos'] ?? null) ? $data['logos'] : [];
    $bgImageUrl = $data['background_media_url'] ?? null;
@endphp

<section class="saas-shell relative overflow-hidden rounded-2xl py-14 sm:py-20">
    @if($bgImageUrl)
        <div class="absolute inset-0">
            <img src="{{ $bgImageUrl }}" alt="" class="h-full w-full object-cover opacity-10">
            <div class="absolute inset-0" style="background: color-mix(in srgb, var(--sn-bg) 70%, transparent);"></div>
        </div>
    @endif

    <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        @if(!empty($data['subtitle']) || !empty($data['title']))
            <div class="mb-12 text-center">
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

        @if(count($stats) > 0)
            @php
                $statCols = count($stats) <= 2 ? 'sm:grid-cols-2' : (count($stats) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4');
            @endphp
            <div class="grid grid-cols-2 gap-8 text-center {{ $statCols }}">
                @foreach($stats as $stat)
                    @if(!empty($stat['value']) && !empty($stat['label']))
                        <div>
                            @if(!empty($stat['icon']))
                                <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-xl"
                                     style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                    <x-dynamic-component :component="'heroicon-o-' . $stat['icon']"
                                        class="h-5 w-5" style="color: var(--sn-primary);" />
                                </div>
                            @endif
                            <div class="text-4xl font-extrabold tracking-tight sm:text-5xl" style="color: var(--sn-primary);">
                                {{ $stat['value'] }}
                            </div>
                            <div class="mt-2 text-sm font-medium" style="color: var(--sn-text);">
                                {{ $stat['label'] }}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @if(count($logos) > 0)
            <div class="mt-14 border-t pt-10" style="border-color: var(--sn-line);">
                <p class="mb-6 text-center text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-muted);">
                    Trusted by
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    @foreach($logos as $logo)
                        @if(!empty($logo['label']))
                            <div class="saas-shell rounded-xl px-5 py-2.5 text-sm font-semibold" style="color: var(--sn-muted);">
                                {{ $logo['label'] }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
