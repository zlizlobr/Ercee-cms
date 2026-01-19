@extends('frontend.layout')

@section('title', $product->name)

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white p-8 shadow-lg">
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="mt-4 text-4xl font-bold text-blue-600">{{ $product->price_formatted }}</p>

            <div class="mt-8">
                <a
                    href="{{ route('frontend.checkout', $product->id) }}"
                    class="inline-block rounded-md bg-blue-600 px-8 py-4 text-lg font-medium text-white transition hover:bg-blue-700"
                >
                    Buy Now
                </a>
            </div>
        </div>
    </div>
@endsection
