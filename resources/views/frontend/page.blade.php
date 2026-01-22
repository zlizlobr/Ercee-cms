@extends('frontend.layout')

@section('title', $page?->title ?? 'Page Not Found')

@section('content')
    @if($page)
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if($page->title)
                <h1 class="mb-8 text-4xl font-bold text-gray-900">{{ $page->title }}</h1>
            @endif

            <div class="space-y-8">
                @foreach($page->getBlocks() as $block)
                    <x-dynamic-component
                        :component="'blocks.' . str_replace('_', '-', $block['type'])"
                        :block="$block"
                    />
                @endforeach
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900">Page Not Found</h1>
                <p class="mt-4 text-gray-600">The page you are looking for does not exist.</p>
                <a href="/" class="mt-6 inline-block rounded-md bg-blue-600 px-6 py-3 text-white hover:bg-blue-700">
                    Go Home
                </a>
            </div>
        </div>
    @endif
@endsection
