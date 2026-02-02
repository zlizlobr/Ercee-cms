<?php

namespace App\Http\Controllers;

use Modules\Commerce\Domain\Order;
use Modules\Commerce\Domain\Payment;
use Modules\Commerce\Domain\Product;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        $page = $this->getPageBySlug('home');
        $navigation = $this->getNavigation();

        return view('frontend.page', [
            'page' => $page,
            'navigation' => $navigation,
        ]);
    }

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

    public function products(): View
    {
        $products = Cache::remember('products:active', 3600, function () {
            return Product::active()->orderBy('name')->get();
        });

        $navigation = $this->getNavigation();

        return view('frontend.products', [
            'products' => $products,
            'navigation' => $navigation,
        ]);
    }

    public function product(int $id): View
    {
        $product = Product::active()->findOrFail($id);
        $navigation = $this->getNavigation();

        return view('frontend.product', [
            'product' => $product,
            'navigation' => $navigation,
        ]);
    }

    public function checkout(int $productId): View
    {
        $product = Product::active()->findOrFail($productId);
        $navigation = $this->getNavigation();

        return view('frontend.checkout', [
            'product' => $product,
            'navigation' => $navigation,
        ]);
    }

    public function thankYou(Request $request): View
    {
        $navigation = $this->getNavigation();

        $orderId = $request->query('order_id');
        $status = $request->query('status', 'success');

        return view('frontend.thank-you', [
            'navigation' => $navigation,
            'status' => $status,
            'orderId' => $orderId,
        ]);
    }

    public function paymentReturn(Request $request): View
    {
        $navigation = $this->getNavigation();
        $sessionId = $request->query('session_id');

        $status = 'pending';
        if ($sessionId) {
            $payment = Payment::where('transaction_id', $sessionId)->first();
            if ($payment) {
                $status = $payment->order->status === Order::STATUS_PAID ? 'success' : 'pending';
            }
        }

        return view('frontend.thank-you', [
            'navigation' => $navigation,
            'status' => $status,
        ]);
    }

    private function getPageBySlug(string $slug): ?Page
    {
        return Cache::remember("page:{$slug}", 3600, function () use ($slug) {
            return Page::published()->where('slug', $slug)->first();
        });
    }

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
