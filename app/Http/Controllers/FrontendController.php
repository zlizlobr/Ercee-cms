<?php

namespace App\Http\Controllers;

use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Render server-side frontend pages for fallback browsing.
 */
class FrontendController extends Controller
{
    /**
     * Render the home page with navigation.
     */
    public function home(): View
    {
        $page = $this->getPageBySlug('home');
        $navigation = $this->getNavigation();

        return view('frontend.page', [
            'page' => $page,
            'navigation' => $navigation,
        ]);
    }

    /**
     * Render a published page by slug with navigation.
     */
    public function page(string $slug): View
    {
        $page = $this->getPageBySlug($slug);

        if (! $page) {
            abort(404);
        }

        $navigation = $this->getNavigation();

        return view('frontend.page', [
            'page' => $page,
            'navigation' => $navigation,
        ]);
    }

    /**
     * Load a published page by slug from cache.
     */
    private function getPageBySlug(string $slug): ?Page
    {
        return Cache::remember("page:{$slug}", 3600, function () use ($slug) {
            return Page::published()->where('slug', $slug)->first();
        });
    }

    /**
     * Load active navigation tree for frontend rendering.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getNavigation(): array
    {
        return Cache::remember('navigation:tree', 3600, function () {
            return Navigation::active()
                ->roots()
                ->ordered()
                ->with(['children' => fn ($q) => $q->active()->ordered()])
                ->get()
                ->map(fn ($item) => $item->toArray())
                ->toArray();
        });
    }
}
