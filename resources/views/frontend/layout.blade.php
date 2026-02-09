<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Ercee CMS'))</title>

    @if(isset($page) && !empty($page->seo_meta['description']))
        <meta name="description" content="{{ $page->seo_meta['description'] }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="bg-white shadow-sm">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900">
                        {{ config('app.name', 'Ercee') }}
                    </a>
                </div>

                @if(!empty($navigation))
                    <div class="hidden md:block">
                        <div class="flex items-center space-x-4">
                            @foreach($navigation as $item)
                                @if(!empty($item['children']))
                                    <div class="relative group">
                                        <button class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                            {{ $item['title'] }}
                                            <svg class="ml-1 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div class="absolute left-0 z-10 mt-2 hidden w-48 rounded-md bg-white py-1 shadow-lg group-hover:block">
                                            @foreach($item['children'] as $child)
                                                <a href="{{ $child['url'] }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    {{ $child['title'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $item['url'] }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                        {{ $item['title'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="md:hidden">
                    <button type="button" id="mobile-menu-button" class="text-gray-700 hover:text-gray-900">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        @if(!empty($navigation))
            <div id="mobile-menu" class="hidden md:hidden">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    @foreach($navigation as $item)
                        <a href="{{ $item['url'] }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                            {{ $item['title'] }}
                        </a>
                        @if(!empty($item['children']))
                            @foreach($item['children'] as $child)
                                <a href="{{ $child['url'] }}" class="block rounded-md px-6 py-2 text-sm text-gray-500 hover:bg-gray-100">
                                    {{ $child['title'] }}
                                </a>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-auto bg-gray-800 py-8 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'Ercee') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
