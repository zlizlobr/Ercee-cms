@php
    $data = $block['data'] ?? $block;
@endphp

<figure class="my-8">
    @if(!empty($data['image']))
        <img
            src="{{ asset('storage/' . $data['image']) }}"
            alt="{{ $data['alt'] ?? '' }}"
            class="w-full rounded-lg shadow-lg"
            loading="lazy"
        >
    @endif

    @if(!empty($data['caption']))
        <figcaption class="mt-2 text-center text-sm text-gray-500">
            {{ $data['caption'] }}
        </figcaption>
    @endif
</figure>
