@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $style = $data['style'] ?? 'primary';

    $buttonClasses = match($style) {
        'secondary' => 'bg-gray-600 hover:bg-gray-700',
        'outline' => 'bg-transparent border-2 border-blue-600 text-blue-600 hover:bg-blue-50',
        default => 'bg-blue-600 hover:bg-blue-700',
    };
@endphp

<div class="rounded-lg bg-blue-50 p-8 text-center">
    @if(!empty($data['title']))
        <h3 class="text-2xl font-bold text-gray-900">{{ $data['title'] }}</h3>
    @endif

    @if(!empty($data['description']))
        <p class="mt-4 text-lg text-gray-600">{{ $data['description'] }}</p>
    @endif

    @if(!empty($data['button_url']) && !empty($data['button_text']))
        <a
            href="{{ $data['button_url'] }}"
            class="mt-6 inline-block rounded-md px-8 py-3 text-lg font-medium text-white transition {{ $buttonClasses }}"
        >
            {{ $data['button_text'] }}
        </a>
    @endif
</div>
