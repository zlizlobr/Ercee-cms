@php
    $categories = is_array($data['categories'] ?? null) ? $data['categories'] : [];
@endphp

<section class="saas-block">
    @if(count($categories) > 0)
        <div class="space-y-8">
            @foreach($categories as $category)
                @if(!empty($category['title']))
                    @php $docs = is_array($category['docs'] ?? null) ? $category['docs'] : []; @endphp
                    <div>
                        <div class="mb-4 flex items-center gap-3">
                            @if(!empty($category['icon']))
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl"
                                     style="background: color-mix(in srgb, var(--sn-primary) 15%, transparent);">
                                    <x-dynamic-component :component="'heroicon-o-' . $category['icon']"
                                        class="h-5 w-5" style="color: var(--sn-primary);" />
                                </div>
                            @endif
                            <h3 class="text-base font-bold" style="color: var(--sn-text);">{{ $category['title'] }}</h3>
                        </div>

                        @if(count($docs) > 0)
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($docs as $doc)
                                    @if(!empty($doc['title']))
                                        <a href="{{ $doc['file_url'] ?? '#' }}"
                                           target="{{ !empty($doc['file_url']) ? '_blank' : '_self' }}"
                                           rel="noopener"
                                           class="saas-shell group flex items-start gap-3 rounded-xl p-4 transition-all duration-200 hover:scale-[1.01]"
                                           style="background: var(--sn-surface-strong);">
                                            <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg"
                                                 style="background: color-mix(in srgb, var(--sn-primary) 10%, transparent);">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                     style="color: var(--sn-primary);">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium leading-tight" style="color: var(--sn-text);">
                                                    {{ $doc['title'] }}
                                                </p>
                                                @if(!empty($doc['description']))
                                                    <p class="mt-0.5 text-xs leading-snug" style="color: var(--sn-muted);">{{ $doc['description'] }}</p>
                                                @endif
                                                @if(!empty($doc['type']) || !empty($doc['size']))
                                                    <p class="mt-1 text-xs font-medium" style="color: var(--sn-primary);">
                                                        {{ implode(' · ', array_filter([$doc['type'] ?? null, $doc['size'] ?? null])) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
