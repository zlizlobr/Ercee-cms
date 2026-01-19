@extends('frontend.layout')

@section('title', 'Thank You')

@section('content')
    <div class="mx-auto max-w-xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center">
            @if($status === 'success')
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="mt-6 text-3xl font-bold text-gray-900">Thank You!</h1>
                <p class="mt-4 text-lg text-gray-600">
                    Your payment was successful. You will receive a confirmation email shortly.
                </p>
            @elseif($status === 'pending')
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="mt-6 text-3xl font-bold text-gray-900">Processing Payment</h1>
                <p class="mt-4 text-lg text-gray-600">
                    Your payment is being processed. You will receive a confirmation email once complete.
                </p>
            @else
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h1 class="mt-6 text-3xl font-bold text-gray-900">Payment Failed</h1>
                <p class="mt-4 text-lg text-gray-600">
                    Unfortunately, your payment could not be processed. Please try again.
                </p>
            @endif

            <div class="mt-8">
                <a
                    href="/"
                    class="inline-block rounded-md bg-blue-600 px-6 py-3 text-white transition hover:bg-blue-700"
                >
                    Return Home
                </a>
            </div>
        </div>
    </div>
@endsection
