@props(['block'])

@php
    $data = $block['data'] ?? $block;
@endphp

<section class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-purple-700 py-20">
    @if(!empty($data['background_image']))
        <div class="absolute inset-0">
            <img
                src="{{ asset('storage/' . $data['background_image']) }}"
                alt=""
                class="h-full w-full object-cover opacity-20"
            >
        </div>
    @endif

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            @if(!empty($data['heading']))
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
                    {{ $data['heading'] }}
                </h1>
            @endif

            @if(!empty($data['subheading']))
                <p class="mx-auto mt-6 max-w-2xl text-xl text-blue-100">
                    {{ $data['subheading'] }}
                </p>
            @endif

            @if(!empty($data['button_url']) && !empty($data['button_text']))
                <div class="mt-10">
                    <a
                        href="{{ $data['button_url'] }}"
                        class="inline-block rounded-md bg-white px-8 py-4 text-lg font-semibold text-blue-600 shadow-lg transition hover:bg-blue-50"
                    >
                        {{ $data['button_text'] }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
