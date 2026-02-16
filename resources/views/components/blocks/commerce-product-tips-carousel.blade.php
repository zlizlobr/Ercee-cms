@props(['block'])

@php
    $data = $block['data'] ?? [];
    $heading = $data['heading'] ?? 'Tipy produktu';
    $description = $data['description'] ?? null;
    $productIds = is_array($data['product_ids'] ?? null) ? $data['product_ids'] : [];
    $maxItems = (int) ($data['max_items'] ?? 8);
    $autoplay = (bool) ($data['autoplay'] ?? true);
    $autoplayMs = (int) ($data['autoplay_ms'] ?? 5000);
@endphp

<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
    <div class="mb-3">
        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Commerce Block Preview</div>
        <h3 class="mt-1 text-lg font-semibold text-gray-900">{{ $heading }}</h3>
        @if(!empty($description))
            <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
        @endif
    </div>

    @if(count($productIds) > 0)
        <div class="flex flex-wrap gap-2">
            @foreach(array_slice($productIds, 0, max($maxItems, 1)) as $id)
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">#{{ $id }}</span>
            @endforeach
        </div>
    @else
        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-3 text-xs text-gray-500">
            No products selected.
        </div>
    @endif

    <div class="mt-3 text-xs text-gray-500">
        {{ $autoplay ? 'Autoplay' : 'Manual' }}
        @if($autoplay)
            ({{ $autoplayMs }} ms)
        @endif
    </div>
</div>
