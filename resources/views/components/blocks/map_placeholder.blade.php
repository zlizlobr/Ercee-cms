<section class="saas-block">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-start">
        <div>
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
                <p class="mt-4 text-sm leading-relaxed sm:text-base" style="color: var(--sn-muted);">
                    {{ $data['description'] }}
                </p>
            @endif
            @if(!empty($data['note']))
                <div class="mt-6 flex items-start gap-3 rounded-xl p-4"
                     style="background: color-mix(in srgb, var(--sn-primary) 8%, transparent); border: 1px solid color-mix(in srgb, var(--sn-primary) 20%, transparent);">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: var(--sn-primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm" style="color: var(--sn-muted);">{{ $data['note'] }}</p>
                </div>
            @endif
        </div>

        <div class="saas-shell overflow-hidden rounded-2xl" style="aspect-ratio: 4/3;">
            <div class="flex h-full flex-col items-center justify-center gap-3" style="background: var(--sn-surface);">
                <svg class="h-12 w-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--sn-primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6-3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <p class="text-xs font-medium" style="color: var(--sn-muted);">Map embed</p>
            </div>
        </div>
    </div>
</section>
