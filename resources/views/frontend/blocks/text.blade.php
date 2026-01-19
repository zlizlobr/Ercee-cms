<div class="prose prose-lg max-w-none">
    @if(!empty($block['data']['heading']))
        <h2 class="text-2xl font-bold text-gray-900">{{ $block['data']['heading'] }}</h2>
    @endif

    @if(!empty($block['data']['body']))
        <div class="mt-4 text-gray-700">
            {!! nl2br(e($block['data']['body'])) !!}
        </div>
    @endif
</div>
