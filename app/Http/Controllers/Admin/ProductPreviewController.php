<?php

namespace App\Http\Controllers\Admin;

use Modules\Commerce\Domain\Product;
use Modules\Commerce\Domain\Services\ProductPricingService;
use App\Domain\Media\Media;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductPreviewController extends Controller
{
    public function __invoke(Product $product, ProductPricingService $pricingService): View
    {
        $product->load(['variants', 'categories', 'tags', 'brands', 'attributeValues.attribute', 'reviews' => fn ($q) => $q->approved()->latest()->take(5)]);

        return view('filament.products.preview', [
            'product' => $product,
            'pricingService' => $pricingService,
            'attachmentUrl' => $this->resolveMediaUrl($product->attachment),
            'galleryUrls' => $this->resolveMediaUrls($product->gallery),
        ]);
    }

    private function resolveMediaUrl(?string $reference, ?string $conversion = null): ?string
    {
        if (blank($reference)) {
            return null;
        }

        if (Str::isUuid($reference)) {
            $media = Media::where('uuid', $reference)->first();
            if (! $media) {
                return null;
            }

            return $conversion ? $media->getUrl($conversion) : $media->getUrl();
        }

        return Storage::disk('public')->url($reference);
    }

    /**
     * @param array<int, string> $references
     * @return array<int, string>
     */
    private function resolveMediaUrls(array $references): array
    {
        return collect($references)
            ->map(fn (string $reference) => $this->resolveMediaUrl($reference))
            ->filter()
            ->values()
            ->all();
    }
}
