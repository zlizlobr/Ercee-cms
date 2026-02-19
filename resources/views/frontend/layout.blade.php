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

    <footer class="saas-footer mt-auto px-4 pt-12 pb-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <a href="/" class="text-lg font-semibold tracking-tight" style="color: var(--sn-text);">
                        {{ config('app.name', 'Ercee') }}
                    </a>
                    <p class="mt-3 text-sm leading-relaxed" style="color: var(--sn-muted);">
                        {{ config('app.description', 'Modern CMS platform built for speed and simplicity.') }}
                    </p>
                    <div class="mt-5 flex items-center gap-3">
                        <a href="#" aria-label="GitHub" class="saas-btn-secondary rounded-lg p-2 transition-all duration-200" style="color: var(--sn-muted);">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        </a>
                        <a href="#" aria-label="Twitter / X" class="saas-btn-secondary rounded-lg p-2 transition-all duration-200" style="color: var(--sn-muted);">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" aria-label="LinkedIn" class="saas-btn-secondary rounded-lg p-2 transition-all duration-200" style="color: var(--sn-muted);">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                @if(!empty($navigation))
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-text);">Navigation</h3>
                        <ul class="mt-4 space-y-2">
                            @foreach(array_slice($navigation, 0, 6) as $item)
                                <li>
                                    <a href="{{ $item['url'] }}" class="saas-nav-link text-sm transition-colors duration-200">{{ $item['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-text);">Resources</h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="/admin" class="saas-nav-link text-sm transition-colors duration-200">Admin panel</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-widest" style="color: var(--sn-text);">Legal</h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="/privacy" class="saas-nav-link text-sm transition-colors duration-200">Privacy policy</a></li>
                        <li><a href="/terms" class="saas-nav-link text-sm transition-colors duration-200">Terms of service</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-2 border-t pt-6 text-xs sm:flex-row sm:items-center sm:justify-between" style="border-color: var(--sn-line); color: var(--sn-muted);">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Ercee') }}. All rights reserved.</p>
                <p>Powered by Ercee CMS</p>
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
