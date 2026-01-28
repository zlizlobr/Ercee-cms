<div class="p-4 border rounded-lg bg-gray-50">
    <div class="text-sm font-medium text-gray-500 mb-2">Service Highlights</div>

    <div class="space-y-4">
        @if(!empty($data['subtitle']))
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ $data['subtitle'] }}</div>
        @endif

        @if(!empty($data['title']))
            <div class="text-lg font-semibold text-gray-900">{{ $data['title'] }}</div>
        @endif

        @if(!empty($data['description']))
            <p class="text-sm text-gray-700">{{ $data['description'] }}</p>
        @endif

        @if(!empty($data['services']) && is_array($data['services']))
            <div>
                <div class="text-sm font-medium text-gray-800 mb-2">Services</div>
                <ul class="space-y-2">
                    @foreach($data['services'] as $service)
                        <li class="text-sm text-gray-700">
                            <div class="font-medium text-gray-900">{{ $service['title'] ?? '-' }}</div>
                            @if(!empty($service['description']))
                                <div>{{ $service['description'] }}</div>
                            @endif
                            @if(!empty($service['link']['url']))
                                <div class="text-xs text-gray-500">{{ $service['link']['url'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($data['cta']['label']))
            <div class="text-sm">
                <span class="font-medium text-gray-900">CTA:</span>
                <span>{{ $data['cta']['label'] }}</span>
                @if(!empty($data['cta']['link']['url']))
                    <span class="text-xs text-gray-500">({{ $data['cta']['link']['url'] }})</span>
                @endif
            </div>
        @endif
    </div>
</div>
