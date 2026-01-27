@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $buttons = $data['buttons'] ?? [];
    $stats = $data['stats'] ?? [];
@endphp

<div class="rounded-lg bg-slate-900 p-6 text-white">
    @if(!empty($data['subtitle']))
        <div class="inline-block rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white">
            {{ $data['subtitle'] }}
        </div>
    @endif

    @if(!empty($data['title']))
        <h3 class="mt-4 text-2xl font-bold">{{ $data['title'] }}</h3>
    @endif

    @if(!empty($data['description']))
        <p class="mt-3 text-sm text-slate-200">{{ $data['description'] }}</p>
    @endif

    <div class="mt-5 text-xs text-slate-300">
        Buttons: {{ is_array($buttons) ? count($buttons) : 0 }} ·
        Stats: {{ is_array($stats) ? count($stats) : 0 }}
    </div>
</div>
