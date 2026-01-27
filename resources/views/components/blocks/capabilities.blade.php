@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $items = $data['items'] ?? [];
@endphp

<div class="space-y-6">
    @if(!empty($data['subtitle']))
        <div class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
            {{ $data['subtitle'] }}
        </div>
    @endif

    @if(!empty($data['title']))
        <h3 class="text-2xl font-bold text-gray-900">{{ $data['title'] }}</h3>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        @foreach($items as $index => $item)
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white">
                        {{ $index + 1 }}
                    </div>
                    <div>
                        @if(!empty($item['title']))
                            <h4 class="text-lg font-semibold text-gray-900">{{ $item['title'] }}</h4>
                        @endif
                        @if(!empty($item['description']))
                            <p class="mt-1 text-sm text-gray-600">{{ $item['description'] }}</p>
                        @endif
                    </div>
                </div>

                @if(!empty($item['features']) && is_array($item['features']))
                    <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-gray-600">
                        @foreach($item['features'] as $feature)
                            <li>{{ is_array($feature) ? ($feature['text'] ?? '') : $feature }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
</div>
