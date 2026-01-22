@props(['block'])

@php
    $data = $block['data'] ?? $block;
@endphp

<div class="prose prose-lg max-w-none">
    @if(!empty($data['heading']))
        <h2 class="text-2xl font-bold text-gray-900">{{ $data['heading'] }}</h2>
    @endif

    @if(!empty($data['body']))
        <div class="mt-4 text-gray-700">
            {!! $data['body'] !!}
        </div>
    @endif
</div>
