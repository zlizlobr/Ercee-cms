@extends('frontend.layout')

@section('title', $page?->title ?? 'Page Not Found')

@section('content')
    @if($page)
        <div class="mx-auto max-w-7xl space-y-8">
            <section class="saas-hero saas-shell overflow-hidden rounded-3xl px-6 py-10 sm:px-10 sm:py-12">
                <div class="flex items-start justify-between gap-6">
                    <div class="max-w-3xl">
                        @if($page->title)
                            <h1 class="text-3xl font-semibold tracking-tight sm:text-5xl">{{ $page->title }}</h1>
                        @endif

                        @if(!empty($page->seo_meta['description']))
                            <p class="mt-4 text-base sm:text-lg" style="color: var(--sn-muted)">
                                {{ $page->seo_meta['description'] }}
                            </p>
                        @endif
                    </div>
                    <span class="saas-glow-dot hidden h-2.5 w-2.5 rounded-full bg-orange-400 sm:block"></span>
                </div>
            </section>

            <div class="saas-prose space-y-6">
                @foreach($page->getBlocks() as $block)
                    <section class="saas-block">
                        <x-dynamic-component
                            :component="'blocks.' . str_replace('_', '-', $block['type'])"
                            :block="$block"
                        />
                    </section>
                @endforeach
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl py-16">
            <div class="saas-shell rounded-3xl p-8 text-center sm:p-12">
                <h1 class="text-3xl font-semibold sm:text-4xl">Page Not Found</h1>
                <p class="mt-4" style="color: var(--sn-muted)">The page you are looking for does not exist.</p>
                <a href="/" class="saas-btn-primary mt-6 inline-block rounded-lg px-6 py-3 text-sm font-semibold sm:text-base">
                    Go Home
                </a>
            </div>
        </div>
    @endif
@endsection
