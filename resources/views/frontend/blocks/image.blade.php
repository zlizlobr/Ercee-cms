<figure class="my-8">
    @if(!empty($block['data']['image']))
        <img
            src="{{ asset('storage/' . $block['data']['image']) }}"
            alt="{{ $block['data']['alt'] ?? '' }}"
            class="w-full rounded-lg shadow-lg"
            loading="lazy"
        >
    @endif

    @if(!empty($block['data']['caption']))
        <figcaption class="mt-2 text-center text-sm text-gray-500">
            {{ $block['data']['caption'] }}
        </figcaption>
    @endif
</figure>
