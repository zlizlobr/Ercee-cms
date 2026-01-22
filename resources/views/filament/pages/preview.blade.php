<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('admin.preview.title') }}: {{ $page->getLocalizedTitle() }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    {{-- Use Tailwind CDN for preview since Vite assets may not be built --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .preview-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .prose { max-width: 65ch; }
        .prose h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .prose p { margin-bottom: 1rem; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    {{-- Preview Banner --}}
    <div class="preview-banner sticky top-0 z-50 px-4 py-3 text-white shadow-lg">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span class="font-semibold">{{ __('admin.preview.mode') }}</span>
                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">
                    {{ $page->status === 'published' ? __('admin.statuses.published') : __('admin.statuses.draft') }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-white/80">{{ $page->getLocalizedTitle() }}</span>
                <a
                    href="{{ route('filament.admin.resources.pages.edit', $page) }}"
                    class="inline-flex items-center gap-2 rounded-md bg-white/20 px-3 py-1.5 text-sm font-medium transition hover:bg-white/30"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('admin.actions.edit') }}
                </a>
                <button
                    onclick="window.close()"
                    class="inline-flex items-center gap-2 rounded-md bg-white/20 px-3 py-1.5 text-sm font-medium transition hover:bg-white/30"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ __('admin.actions.close') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Page Content --}}
    <main>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if($page->getLocalizedTitle())
                <h1 class="mb-8 text-4xl font-bold text-gray-900">{{ $page->getLocalizedTitle() }}</h1>
            @endif

            <div class="space-y-8">
                @foreach($page->getBlocks() as $block)
                    @php
                        $blockType = str_replace('_', '-', $block['type']);
                        $componentName = 'blocks.' . $blockType;
                    @endphp

                    @if(View::exists('components.' . $componentName))
                        <x-dynamic-component
                            :component="$componentName"
                            :block="$block"
                        />
                    @elseif(View::exists('frontend.blocks.' . $block['type']))
                        @include('frontend.blocks.' . $block['type'], ['block' => $block])
                    @else
                        <div class="rounded-lg border-2 border-dashed border-yellow-400 bg-yellow-50 p-6">
                            <p class="text-sm text-yellow-800">
                                {{ __('admin.preview.unknown_block', ['type' => $block['type']]) }}
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </main>

    {{-- Page Info Footer --}}
    <footer class="mt-8 border-t bg-gray-100 py-4">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-4">
                    <span><strong>Slug:</strong> /{{ $page->slug }}</span>
                    <span><strong>ID:</strong> {{ $page->id }}</span>
                    <span><strong>{{ __('admin.labels.status') }}:</strong> {{ __('admin.statuses.' . $page->status) }}</span>
                </div>
                <div class="flex items-center gap-4">
                    @if($page->published_at)
                        <span><strong>{{ __('admin.labels.published_at') }}:</strong> {{ $page->published_at->format('d.m.Y H:i') }}</span>
                    @endif
                    <span><strong>{{ __('admin.labels.updated_at') }}:</strong> {{ $page->updated_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
