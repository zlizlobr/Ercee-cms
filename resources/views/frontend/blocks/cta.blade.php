<div class="rounded-lg bg-blue-50 p-8 text-center">
    @if(!empty($block['data']['title']))
        <h3 class="text-2xl font-bold text-gray-900">{{ $block['data']['title'] }}</h3>
    @endif

    @if(!empty($block['data']['description']))
        <p class="mt-4 text-lg text-gray-600">{{ $block['data']['description'] }}</p>
    @endif

    @if(!empty($block['data']['button_url']) && !empty($block['data']['button_text']))
        <a
            href="{{ $block['data']['button_url'] }}"
            class="mt-6 inline-block rounded-md bg-blue-600 px-8 py-3 text-lg font-medium text-white transition hover:bg-blue-700"
        >
            {{ $block['data']['button_text'] }}
        </a>
    @endif
</div>
