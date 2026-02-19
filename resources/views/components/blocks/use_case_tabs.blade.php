@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
@endphp

<section class="saas-block" x-data="{ activeTab: 0 }">
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

    @if(count($items) > 0)
        <div class="overflow-x-auto">
            <div class="saas-shell mb-6 inline-flex min-w-full gap-1 overflow-hidden rounded-xl p-1 sm:min-w-0"
                 style="background: var(--sn-surface);">
                @foreach($items as $idx => $item)
                    @if(!empty($item['industry']))
                        <button @click="activeTab = {{ $idx }}"
                                class="flex flex-shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200"
                                :class="activeTab === {{ $idx }}
                                    ? 'text-white'
                                    : ''"
                                :style="activeTab === {{ $idx }}
                                    ? 'background: var(--sn-primary); box-shadow: 0 4px 14px var(--sn-glow);'
                                    : 'color: var(--sn-muted);'">
                            @if(!empty($item['icon']))
                                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                                    class="h-4 w-4" />
                            @endif
                            {{ $item['industry'] }}
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        @foreach($items as $idx => $item)
            @if(!empty($item['industry']))
                @php
                    $results = is_array($item['results'] ?? null) ? $item['results'] : [];
                    $imageUrl = $item['image_media_url'] ?? null;
                @endphp
                <div x-show="activeTab === {{ $idx }}" x-transition:enter="transition duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="saas-shell grid grid-cols-1 gap-8 rounded-2xl p-6 sm:p-8 lg:grid-cols-2 lg:items-start"
                         style="background: var(--sn-surface-strong);">
                        <div>
                            <h3 class="text-lg font-bold" style="color: var(--sn-text);">{{ $item['industry'] }}</h3>

                            @if(!empty($item['challenge']))
                                <div class="mt-5">
                                    <div class="mb-1 text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-primary);">
                                        Challenge
                                    </div>
                                    <p class="text-sm leading-relaxed" style="color: var(--sn-muted);">{{ $item['challenge'] }}</p>
                                </div>
                            @endif

                            @if(!empty($item['solution']))
                                <div class="mt-4">
                                    <div class="mb-1 text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-primary);">
                                        Solution
                                    </div>
                                    <p class="text-sm leading-relaxed" style="color: var(--sn-muted);">{{ $item['solution'] }}</p>
                                </div>
                            @endif

                            @if(count($results) > 0)
                                <div class="mt-4">
                                    <div class="mb-2 text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-primary);">
                                        Results
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach($results as $result)
                                            @if(!empty($result['text']))
                                                <li class="flex items-start gap-2 text-sm" style="color: var(--sn-muted);">
                                                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                         style="color: var(--sn-primary);">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    {{ $result['text'] }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        @if($imageUrl)
                            <div class="overflow-hidden rounded-xl" style="aspect-ratio: 4/3;">
                                <img src="{{ $imageUrl }}" alt="{{ $item['industry'] }}" class="h-full w-full object-cover">
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</section>
