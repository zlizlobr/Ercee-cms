@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
@endphp

<section class="saas-block">
    @if(!empty($data['title']) || !empty($data['subtitle']))
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
    @endif

    @if(count($items) > 0)
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $item)
                @if(!empty($item['name']))
                    @php
                        $features = is_array($item['features'] ?? null) ? $item['features'] : [];
                        $certs = is_array($item['certifications'] ?? null) ? $item['certifications'] : [];
                    @endphp
                    <div class="saas-shell flex flex-col overflow-hidden rounded-2xl" style="background: var(--sn-surface-strong);">
                        @if(!empty($item['image_media_url']))
                            <div class="overflow-hidden" style="aspect-ratio: 16/9;">
                                <img src="{{ $item['image_media_url'] }}" alt="{{ $item['name'] }}"
                                     class="h-full w-full object-cover">
                            </div>
                        @endif
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-base font-bold" style="color: var(--sn-text);">{{ $item['name'] }}</h3>
                                    @if(!empty($item['location']))
                                        <p class="mt-0.5 flex items-center gap-1 text-xs" style="color: var(--sn-muted);">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $item['location'] }}
                                        </p>
                                    @endif
                                </div>
                                @if(!empty($item['type']))
                                    <span class="flex-shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background: color-mix(in srgb, var(--sn-primary) 12%, transparent); color: var(--sn-primary);">
                                        {{ $item['type'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 space-y-1 text-xs" style="color: var(--sn-muted);">
                                @if(!empty($item['size']))
                                    <p>{{ __('Size') }}: {{ $item['size'] }}</p>
                                @endif
                                @if(!empty($item['hours']))
                                    <p>{{ __('Hours') }}: {{ $item['hours'] }}</p>
                                @endif
                                @if(!empty($item['manager']))
                                    <p>{{ __('Manager') }}: {{ $item['manager'] }}</p>
                                @endif
                            </div>

                            @if(count($features) > 0)
                                <ul class="mt-3 space-y-1">
                                    @foreach(array_slice($features, 0, 4) as $feature)
                                        @if(!empty($feature['text']))
                                            <li class="flex items-center gap-1.5 text-xs" style="color: var(--sn-muted);">
                                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                     style="color: var(--sn-primary);">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ $feature['text'] }}
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif

                            @if(!empty($item['phone']) || !empty($item['email']))
                                <div class="mt-4 flex flex-wrap gap-3 border-t pt-3" style="border-color: var(--sn-line);">
                                    @if(!empty($item['phone']))
                                        <a href="tel:{{ $item['phone'] }}" class="text-xs transition-colors duration-200"
                                           style="color: var(--sn-primary);">{{ $item['phone'] }}</a>
                                    @endif
                                    @if(!empty($item['email']))
                                        <a href="mailto:{{ $item['email'] }}" class="text-xs transition-colors duration-200"
                                           style="color: var(--sn-primary);">{{ $item['email'] }}</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
