@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $imageUrl = $data['image_url_medium'] ?? $data['image_url'] ?? null;

    // Fallback for legacy format
    if (!$imageUrl && !empty($data['image'])) {
        $imageUrl = asset('storage/' . $data['image']);
    }
@endphp

<figure class="my-8">
    @if($imageUrl)
        <img
            src="{{ $imageUrl }}"
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
