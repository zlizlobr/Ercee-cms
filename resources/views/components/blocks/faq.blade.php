@php
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
@endphp

<section class="saas-block" x-data>
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

    @if(count($items) > 0)
        <div class="mt-8 space-y-2">
            @foreach($items as $i => $item)
                @if(!empty($item['question']))
                    <div x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }"
                         class="overflow-hidden rounded-xl border transition-all duration-200"
                         style="border-color: var(--sn-line); background: var(--sn-surface);">
                        <button @click="open = !open"
                                class="flex w-full items-center justify-between px-5 py-4 text-left transition-colors duration-150"
                                style="color: var(--sn-text);">
                            <span class="text-sm font-semibold sm:text-base">{{ $item['question'] }}</span>
                            <span class="ml-4 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-45': open }">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="color: var(--sn-primary);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                        </button>
                        <div x-show="open" x-collapse class="border-t px-5 pb-4 pt-3 text-sm leading-relaxed"
                             style="border-color: var(--sn-line); color: var(--sn-muted);">
                            {{ $item['answer'] ?? '' }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
