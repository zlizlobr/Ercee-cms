@extends('frontend.layout')

@section('title', 'Products')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-4xl font-bold text-gray-900">Products</h1>

        @if($products->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-600">No products available at the moment.</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($products as $product)
                    <div class="rounded-lg bg-white p-6 shadow-md transition hover:shadow-lg">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $product->name }}</h2>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ $product->price_formatted }}</p>
                        <a
                            href="{{ route('frontend.checkout', $product->id) }}"
                            class="mt-4 block rounded-md bg-blue-600 px-6 py-3 text-center text-white transition hover:bg-blue-700"
                        >
                            Buy Now
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
