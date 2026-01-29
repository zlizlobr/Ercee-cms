<div class="p-4 border rounded-lg bg-gray-50">
    <div class="text-sm font-medium text-gray-500 mb-2">FAQ</div>
    <div class="space-y-2">
        @foreach($data ?? [] as $key => $value)
            <div class="text-sm">
                <span class="font-medium">{{ $key }}:</span>
                <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
            </div>
        @endforeach
    </div>
</div>
