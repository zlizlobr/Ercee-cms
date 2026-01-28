@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $bgImageUrl = $data['background_image_url_large'] ?? $data['background_image_url'] ?? null;
    $ctaPrimary = $data['cta_primary'] ?? null;
    $ctaSecondary = $data['cta_secondary'] ?? null;
    $stats = is_array($data['stats'] ?? null) ? $data['stats'] : [];

    // Fallback for legacy format
    if (!$bgImageUrl && !empty($data['background_image'])) {
        $bgImageUrl = asset('storage/' . $data['background_image']);
    }
@endphp

<section class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-purple-700 py-20">
    @if($bgImageUrl)
        <div class="absolute inset-0">
            <img
                src="{{ $bgImageUrl }}"
                alt=""
                class="h-full w-full object-cover opacity-20"
            >
        </div>
    @endif

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            @if(!empty($data['title']))
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
                    {{ $data['title'] }}
                </h1>
            @endif

            @if(!empty($data['subtitle']))
                <p class="mx-auto mt-6 max-w-2xl text-xl text-blue-100">
                    {{ $data['subtitle'] }}
                </p>
            @endif

            @if(!empty($data['description']))
                <p class="mx-auto mt-4 max-w-2xl text-lg text-blue-100">
                    {{ $data['description'] }}
                </p>
            @endif

            @if(!empty($ctaPrimary['url']) && !empty($ctaPrimary['label']))
                <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <a
                        href="{{ $ctaPrimary['url'] }}"
                        class="inline-block rounded-md bg-white px-8 py-4 text-lg font-semibold text-blue-600 shadow-lg transition hover:bg-blue-50"
                    >
                        {{ $ctaPrimary['label'] }}
                    </a>
                    @if(!empty($ctaSecondary['url']) && !empty($ctaSecondary['label']))
                        <a
                            href="{{ $ctaSecondary['url'] }}"
                            class="inline-block rounded-md border border-white/70 px-8 py-4 text-lg font-semibold text-white transition hover:bg-white/10"
                        >
                            {{ $ctaSecondary['label'] }}
                        </a>
                    @endif
                </div>
            @endif
        </div>

        @if(count($stats) > 0)
            <div class="mx-auto mt-12 grid max-w-4xl grid-cols-2 gap-6 border-t border-white/20 pt-8 text-center sm:grid-cols-4">
                @foreach($stats as $stat)
                    @if(!empty($stat['value']) && !empty($stat['label']))
                        <div>
                            <div class="text-2xl font-bold text-white">{{ $stat['value'] }}</div>
                            <div class="text-sm text-blue-100">{{ $stat['label'] }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
