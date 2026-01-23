<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('admin.preview.title') }}: {{ $product->name }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>

    <style>
        .preview-banner {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    {{-- Preview Banner --}}
    <div class="preview-banner sticky top-0 z-50 px-4 py-3 text-white shadow-lg">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span class="font-semibold">{{ __('admin.preview.mode') }}</span>
                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">
                    {{ $product->active ? __('admin.statuses.active') : __('admin.statuses.inactive') }}
                </span>
                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs uppercase">
                    {{ $product->type }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-white/80">{{ $product->name }}</span>
                <a
                    href="{{ route('filament.admin.resources.products.edit', $product) }}"
                    class="inline-flex items-center gap-2 rounded-md bg-white/20 px-3 py-1.5 text-sm font-medium transition hover:bg-white/30"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('admin.actions.edit') }}
                </a>
                <button
                    onclick="window.close()"
                    class="inline-flex items-center gap-2 rounded-md bg-white/20 px-3 py-1.5 text-sm font-medium transition hover:bg-white/30"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ __('admin.actions.close') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Product Content --}}
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Left: Images --}}
            <div class="space-y-4">
                @if($product->attachment)
                    <div class="overflow-hidden rounded-lg bg-white shadow">
                        <img
                            src="{{ Storage::disk('public')->url($product->attachment) }}"
                            alt="{{ $product->name }}"
                            class="h-96 w-full object-cover"
                        >
                    </div>
                @else
                    <div class="flex h-96 items-center justify-center rounded-lg bg-gray-200">
                        <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                @if(count($product->gallery) > 0)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->gallery as $image)
                            <div class="overflow-hidden rounded-lg bg-white shadow">
                                <img
                                    src="{{ Storage::disk('public')->url($image) }}"
                                    alt="Gallery"
                                    class="h-24 w-full object-cover"
                                >
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Info --}}
            <div class="space-y-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $product->slug }}</p>
                </div>

                {{-- Short Description --}}
                @if($product->short_description)
                    <div class="text-lg text-gray-600">
                        {{ $product->short_description }}
                    </div>
                @endif

                {{-- Price --}}
                <div class="rounded-lg bg-white p-6 shadow">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('admin.labels.price') }}</h2>
                    @if($product->isVariable())
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            {{ $pricingService->getPriceRangeFormatted($product) }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">Price varies by variant</p>
                    @else
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            {{ $product->price_formatted }}
                        </p>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('admin.product.fields.description') }}</h2>
                        <div class="prose prose-sm mt-4 max-w-none text-gray-600">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif

                {{-- Taxonomies --}}
                @if($product->categories->count() || $product->tags->count() || $product->brands->count())
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-900">Taxonomies</h2>
                        <div class="mt-4 space-y-3">
                            @if($product->categories->count())
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Categories:</span>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @foreach($product->categories as $category)
                                            <span class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($product->tags->count())
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tags:</span>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @foreach($product->tags as $tag)
                                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-800">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($product->brands->count())
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Brands:</span>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @foreach($product->brands as $brand)
                                            <span class="rounded-full bg-purple-100 px-3 py-1 text-sm text-purple-800">
                                                {{ $brand->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Attributes --}}
                @if($product->attributeValues->count())
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-900">Attributes</h2>
                        <div class="mt-4 space-y-2">
                            @foreach($product->attributeValues->groupBy('attribute.name') as $attributeName => $values)
                                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                    <span class="font-medium text-gray-700">{{ $attributeName }}</span>
                                    <span class="text-gray-600">{{ $values->pluck('value')->join(', ') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Variants --}}
                @if($product->isVariable() && $product->variants->count())
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-900">Variants ({{ $product->variants->count() }})</h2>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">SKU</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Price</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Stock</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">Attributes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($product->variants as $variant)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-2 text-sm font-medium text-gray-900">{{ $variant->sku }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-600">{{ $variant->price_formatted }}</td>
                                            <td class="whitespace-nowrap px-4 py-2">
                                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $variant->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $variant->stock }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600">
                                                {{ $variant->attributeValues->pluck('value')->join(', ') ?: '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Reviews --}}
                @if($product->reviews->count())
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Reviews</h2>
                        <div class="mt-4 space-y-4">
                            @foreach($product->reviews as $review)
                                <div class="border-b border-gray-100 pb-4 last:border-0">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-900">{{ $review->author_name }}</span>
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">{{ $review->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Product Info Footer --}}
    <footer class="mt-8 border-t bg-gray-100 py-4">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-4">
                    <span><strong>Slug:</strong> {{ $product->slug }}</span>
                    <span><strong>ID:</strong> {{ $product->id }}</span>
                    <span><strong>Type:</strong> {{ ucfirst($product->type) }}</span>
                    <span><strong>Status:</strong> {{ $product->active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <span><strong>Variants:</strong> {{ $product->variants->count() }}</span>
                    <span><strong>Reviews:</strong> {{ $product->reviews->count() }}</span>
                    <span><strong>{{ __('admin.labels.updated_at') }}:</strong> {{ $product->updated_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
