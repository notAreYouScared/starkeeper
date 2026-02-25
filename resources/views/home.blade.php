<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarKeeper – Star Citizen Organisation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
        document.documentElement.classList.add('dark');
    </script>
</head>
<body class="min-h-full bg-gray-950 text-gray-100 antialiased">

    {{-- ─── Header ─── --}}
    <header class="border-b border-white/10 bg-gray-900/80 backdrop-blur sticky top-0 z-10">
        <div class="mx-auto max-w-5xl px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ url('image/logo.png') }}"
                     alt="StarKeeper Logo"
                     class="h-8 w-auto">
                <span class="text-xl font-bold tracking-wider text-blue-400">Starkeeper Industries</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('home') }}"
                   class="text-blue-400">Home</a>
                <a href="{{ route('org-hierarchy') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Hierarchy</a>
                <a href="{{ route('history') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">History</a>
                <a href="{{ route('manifesto') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Manifesto</a>
                <a href="{{ route('charter') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Charter</a>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                       class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Admin Panel →</a>
                @else
                    <a href="{{ route('filament.admin.auth.login') }}"
                       class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Sign in →</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- ─── Hero Banner ─── --}}
    <div class="relative w-full overflow-hidden" style="max-height: 340px;">
        <img src="{{ url('image/banner.png') }}"
             alt="StarKeeper Banner"
             class="w-full object-cover object-center"
             style="max-height: 340px;">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/5 to-transparent"></div>
        </div>
    </div>

    <main class="mx-auto max-w-5xl px-4 py-12 space-y-12">

        {{-- ─── Tagline & Links ─── --}}
        <section class="text-center space-y-6">
            @if($page)
            <div class="prose prose-invert max-w-2xl mx-auto text-gray-300 leading-relaxed">
                {!! \Illuminate\Support\Str::markdown($page->content) !!}
            </div>
            @endif

            <div class="flex flex-wrap items-center justify-center gap-4">
                {{-- Discord button --}}
                <a href="https://discord.gg/starkeeper"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-colors">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                    </svg>
                    Join our Discord
                </a>

                {{-- RSI Org page --}}
                <a href="https://robertsspaceindustries.com/en/orgs/STARKEEPER"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-lg border border-white/20 hover:border-blue-400/60 bg-white/5 hover:bg-blue-400/10 px-6 py-3 text-sm font-semibold text-gray-200 shadow-lg transition-colors">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    View on RSI
                </a>
            </div>
        </section>

        {{-- ─── Navigation Cards ─── --}}
        <section>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <a href="{{ route('org-hierarchy') }}"
                   class="group flex flex-col gap-2 rounded-xl border border-blue-400/20 bg-blue-400/5 hover:bg-blue-400/10 hover:border-blue-400/40 p-5 transition-colors">
                    <span class="text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>
                    </span>
                    <span class="font-semibold text-white group-hover:text-blue-300 transition-colors">Org Hierarchy</span>
                    <span class="text-xs text-gray-400">Browse leadership, divisions, and teams.</span>
                </a>

                <a href="{{ route('history') }}"
                   class="group flex flex-col gap-2 rounded-xl border border-yellow-400/20 bg-yellow-400/5 hover:bg-yellow-400/10 hover:border-yellow-400/40 p-5 transition-colors">
                    <span class="text-yellow-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="font-semibold text-white group-hover:text-yellow-300 transition-colors">History</span>
                    <span class="text-xs text-gray-400">Our founding story and journey through the 'verse.</span>
                </a>

                <a href="{{ route('manifesto') }}"
                   class="group flex flex-col gap-2 rounded-xl border border-green-400/20 bg-green-400/5 hover:bg-green-400/10 hover:border-green-400/40 p-5 transition-colors">
                    <span class="text-green-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                        </svg>
                    </span>
                    <span class="font-semibold text-white group-hover:text-green-300 transition-colors">Manifesto</span>
                    <span class="text-xs text-gray-400">Our values, vision, and purpose as an org.</span>
                </a>

                <a href="{{ route('charter') }}"
                   class="group flex flex-col gap-2 rounded-xl border border-purple-400/20 bg-purple-400/5 hover:bg-purple-400/10 hover:border-purple-400/40 p-5 transition-colors">
                    <span class="text-purple-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-4.5 5.25h4.5m-4.5-5.25h.008v.008H8.25V10.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V15zm0 2.25h.008v.008H8.25v-.008zm2.498-6.75h.007v.008h-.007V10.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V15zm0 2.25h.007v.008h-.007v-.008zm2.247-4.5h.007v.008h-.007V12.75zm0 2.25h.007v.008h-.007V15zm0 2.25h.007v.008h-.007v-.008zM6 18.75a.75.75 0 01-.75-.75V6.75A.75.75 0 016 6h12a.75.75 0 01.75.75v11.25a.75.75 0 01-.75.75H6z"/>
                        </svg>
                    </span>
                    <span class="font-semibold text-white group-hover:text-purple-300 transition-colors">Charter</span>
                    <span class="text-xs text-gray-400">Rules, governance, and member expectations.</span>
                </a>

            </div>
        </section>

    </main>

    <footer class="border-t border-white/10 mt-12">
        <div class="mx-auto max-w-5xl px-4 py-6 flex flex-wrap items-center justify-between gap-4 text-xs text-gray-500">
            <span>&copy; {{ date('Y') }} Starkeeper. All rights reserved.</span>
            <div class="flex items-center gap-4">
                <a href="https://robertsspaceindustries.com/en/orgs/STARKEEPER"
                   target="_blank" rel="noopener noreferrer"
                   class="hover:text-gray-300 transition-colors">RSI Org Page</a>
                <a href="https://discord.gg/starkeeper"
                   target="_blank" rel="noopener noreferrer"
                   class="hover:text-gray-300 transition-colors">Discord</a>
            </div>
        </div>
    </footer>

</body>
</html>
