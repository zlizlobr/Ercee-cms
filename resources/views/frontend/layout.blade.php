<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="theme-dark">
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
<body class="saas-neon min-h-screen antialiased transition-colors duration-300">
    <header class="sticky top-0 z-50 border-b border-transparent px-4 pt-4 sm:px-6 lg:px-8">
        <nav class="saas-shell mx-auto max-w-7xl rounded-2xl px-4 sm:px-6">
            <div class="flex h-16 items-center justify-between gap-4">
                <a href="/" class="text-lg font-semibold tracking-tight sm:text-xl">
                    {{ config('app.name', 'Ercee') }}
                </a>

                @if(!empty($navigation))
                    <div class="hidden md:block">
                        <div class="flex items-center gap-5">
                            @foreach($navigation as $item)
                                @if(!empty($item['children']))
                                    <div class="relative group">
                                        <button class="saas-nav-link inline-flex items-center gap-1 text-sm font-medium">
                                            {{ $item['title'] }}
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div class="saas-shell absolute left-0 z-10 mt-3 hidden min-w-48 rounded-xl p-2 group-hover:block">
                                            @foreach($item['children'] as $child)
                                                <a href="{{ $child['url'] }}" class="saas-nav-link block rounded-lg px-3 py-2 text-sm">
                                                    {{ $child['title'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $item['url'] }}" class="saas-nav-link text-sm font-medium">{{ $item['title'] }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-2">
                    <button type="button" id="theme-toggle" class="saas-btn-secondary rounded-lg px-3 py-2 text-sm font-medium">
                        White mode
                    </button>
                    <button type="button" id="mobile-menu-button" class="saas-btn-secondary rounded-lg p-2 md:hidden" aria-label="Open menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            @if(!empty($navigation))
                <div id="mobile-menu" class="saas-mobile-menu hidden pb-3 md:hidden">
                    <div class="space-y-1 pt-3">
                        @foreach($navigation as $item)
                            <a href="{{ $item['url'] }}" class="saas-nav-link block rounded-md px-2 py-2 text-sm font-medium">
                                {{ $item['title'] }}
                            </a>
                            @if(!empty($item['children']))
                                @foreach($item['children'] as $child)
                                    <a href="{{ $child['url'] }}" class="saas-nav-link block rounded-md px-5 py-2 text-xs">
                                        {{ $child['title'] }}
                                    </a>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </nav>
    </header>

    <main class="px-4 pb-12 pt-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <footer class="saas-footer mt-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl text-sm" style="color: var(--sn-muted)">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Ercee') }}. All rights reserved.</p>
                <p>Neon SaaS template (dark + white mode)</p>
            </div>
        </div>
    </footer>

    <script>
        const root = document.documentElement;
        const themeToggle = document.getElementById('theme-toggle');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        const setTheme = (theme) => {
            root.classList.toggle('theme-dark', theme === 'dark');
            root.classList.toggle('theme-light', theme === 'light');
            localStorage.setItem('saas-theme', theme);
            if (themeToggle) {
                themeToggle.textContent = theme === 'dark' ? 'White mode' : 'Dark mode';
            }
        };

        const savedTheme = localStorage.getItem('saas-theme') || 'dark';
        setTheme(savedTheme);

        themeToggle?.addEventListener('click', () => {
            const next = root.classList.contains('theme-dark') ? 'light' : 'dark';
            setTheme(next);
        });

        mobileMenuButton?.addEventListener('click', () => {
            mobileMenu?.classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
