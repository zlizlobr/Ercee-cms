@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
@endphp

<section class="saas-block">
    @if(!empty($data['title']) || !empty($data['subtitle']))
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

    @if(count($items) > 0)
        <div class="space-y-6">
            @foreach($items as $idx => $item)
                @if(!empty($item['title']))
                    @php
                        $imageUrl = $item['image_media_url'] ?? null;
                        $features = is_array($item['features'] ?? null) ? $item['features'] : [];
                        $reverse = $idx % 2 !== 0;
                    @endphp
                    <div class="saas-shell grid grid-cols-1 gap-8 overflow-hidden rounded-2xl p-6 sm:p-8 lg:grid-cols-2 lg:items-center {{ $reverse ? 'lg:[direction:rtl]' : '' }}"
                         style="background: var(--sn-surface-strong);">
                        <div class="{{ $reverse ? 'lg:[direction:ltr]' : '' }}">
                            @if(!empty($item['icon']))
                                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl"
                                     style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                    <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                                        class="h-5 w-5" style="color: var(--sn-primary);" />
                                </div>
                            @endif
                            <h3 class="text-lg font-bold" style="color: var(--sn-text);">{{ $item['title'] }}</h3>
                            @if(!empty($item['description']))
                                <p class="mt-3 text-sm leading-relaxed" style="color: var(--sn-muted);">
                                    {{ $item['description'] }}
                                </p>
                            @endif
                            @if(count($features) > 0)
                                <ul class="mt-4 space-y-2">
                                    @foreach($features as $feature)
                                        @if(!empty($feature['text']))
                                            <li class="flex items-center gap-2 text-sm" style="color: var(--sn-muted);">
                                                <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                     style="color: var(--sn-primary);">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ $feature['text'] }}
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        @if($imageUrl)
                            <div class="overflow-hidden rounded-xl {{ $reverse ? 'lg:[direction:ltr]' : '' }}" style="aspect-ratio: 4/3;">
                                <img src="{{ $imageUrl }}" alt="{{ $item['title'] }}" class="h-full w-full object-cover">
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
