<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History – StarKeeper</title>
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
                <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <img src="{{ public_path('image/logo.png') }}"
                         alt="StarKeeper Logo"
                         class="h-8 w-auto">
                    <span class="text-xl font-bold tracking-wider text-blue-400">StarKeeper</span>
                </a>
                <span class="text-gray-500">/</span>
                <span class="text-sm text-gray-300">History</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('org-hierarchy') }}" class="text-gray-300 hover:text-blue-400 transition-colors">Org Hierarchy</a>
                <a href="{{ route('history') }}" class="text-yellow-400">History</a>
                <a href="{{ route('manifesto') }}" class="text-gray-300 hover:text-blue-400 transition-colors">Manifesto</a>
                <a href="{{ route('charter') }}" class="text-gray-300 hover:text-blue-400 transition-colors">Charter</a>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                       class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Admin Panel →</a>
                @else
                    <a href="{{ route('filament.admin.auth.login') }}"
                       class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Sign in →</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-12">

        <div class="flex items-center gap-3 mb-8">
            <div class="h-10 w-1 rounded bg-yellow-400"></div>
            <h1 class="text-3xl font-black tracking-widest uppercase text-yellow-400">History</h1>
        </div>

        <div class="space-y-6 text-gray-300 leading-relaxed">

            <p>
                StarKeeper was founded by a tight-knit group of pilots and adventurers who shared one conviction:
                that the vast expanse of the 'verse is best explored—and defended—together.
                What began as a handful of friends meeting in a small hangar on ArcCorp has grown into one of the most
                respected multi-role organisations in the galaxy.
            </p>

            <p>
                In the early days our founders made do with whatever ships they could scrape together, taking on
                salvage contracts and security patrols to fund the dream of something bigger.
                Those humble missions forged bonds of trust and taught us the values that still define us today:
                reliability, discipline, and loyalty to one another above all else.
            </p>

            <p>
                As our reputation grew, so did our numbers.
                Skilled traders, battle-hardened fighters, daring racers, and tireless industrialists answered the call,
                each bringing their own expertise to bear.
                We organised ourselves into specialised divisions—Security, Industry, and Racing—giving every member
                a home where their talents could shine while contributing to the whole.
            </p>

            <p>
                Today StarKeeper stands as a fully realised multi-discipline organisation.
                We have participated in large-scale fleet operations, defended allied factions, built industrial
                supply chains, and competed in racing circuits across the 'verse.
                Through every challenge we have remained true to the founding spirit: a crew that looks after
                its own and leaves its mark on the universe.
            </p>

            <p>
                Our journey is far from over.
                With the continuous expansion of the 'verse we look forward to new systems to explore,
                new alliances to forge, and new chapters of StarKeeper history yet to be written.
            </p>

        </div>

    </main>

    <footer class="border-t border-white/10 mt-12">
        <div class="mx-auto max-w-5xl px-4 py-6 flex flex-wrap items-center justify-between gap-4 text-xs text-gray-500">
            <span>&copy; {{ date('Y') }} StarKeeper. All rights reserved.</span>
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
