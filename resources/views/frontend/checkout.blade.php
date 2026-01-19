@extends('frontend.layout')

@section('title', 'Checkout - ' . $product->name)

@section('content')
    <div class="mx-auto max-w-xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">Checkout</h1>

        <div class="mb-8 rounded-lg bg-gray-50 p-6">
            <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-gray-600">{{ $product->name }}</span>
                <span class="text-xl font-bold text-gray-900">{{ $product->price_formatted }}</span>
            </div>
        </div>

        <form id="checkout-form" class="space-y-6" onsubmit="return submitCheckout(event)">
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="email"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="your@email.com"
                >
            </div>

            <div id="checkout-error" class="hidden rounded-md bg-red-50 p-4 text-red-700"></div>

            <button
                type="submit"
                id="checkout-button"
                class="w-full rounded-md bg-blue-600 px-6 py-4 text-lg font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span id="button-text">Pay {{ $product->price_formatted }}</span>
                <span id="button-loading" class="hidden">
                    <svg class="inline h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
async function submitCheckout(event) {
    event.preventDefault();

    const form = document.getElementById('checkout-form');
    const button = document.getElementById('checkout-button');
    const buttonText = document.getElementById('button-text');
    const buttonLoading = document.getElementById('button-loading');
    const errorDiv = document.getElementById('checkout-error');

    errorDiv.classList.add('hidden');
    button.disabled = true;
    buttonText.classList.add('hidden');
    buttonLoading.classList.remove('hidden');

    const formData = new FormData(form);

    try {
        const response = await axios.post('/api/v1/checkout', {
            product_id: formData.get('product_id'),
            email: formData.get('email')
        });

        if (response.data.data?.redirect_url) {
            window.location.href = response.data.data.redirect_url;
        }
    } catch (error) {
        button.disabled = false;
        buttonText.classList.remove('hidden');
        buttonLoading.classList.add('hidden');

        const message = error.response?.data?.error || 'An error occurred. Please try again.';
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }

    return false;
}
</script>
@endpush
