<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manifesto – StarKeeper</title>
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
                    <img src="{{ url('image/logo.png') }}"
                         alt="StarKeeper Logo"
                         class="h-8 w-auto">
                    <span class="text-xl font-bold tracking-wider text-blue-400">Starkeeper Industries</span>
                </a>
                <span class="text-gray-500">/</span>
                <span class="text-sm text-gray-300">Manifesto</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('home') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Home</a>
                <a href="{{ route('org-hierarchy') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Org Hierarchy</a>
                <a href="{{ route('history') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">History</a>
                <a href="{{ route('manifesto') }}"
                   class="text-green-400">Manifesto</a>
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
    <main class="mx-auto max-w-3xl px-4 py-12">
        <div class="flex items-center gap-3 mb-8">
            <div class="h-10 w-1 rounded bg-green-400"></div>
            <h1 class="text-3xl font-black tracking-widest uppercase text-green-400">Manifesto</h1>
        </div>
        <div class="space-y-8 text-gray-300 leading-relaxed">
            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">Our Mission</h2>
                <p>
                    Explore boldly, operate together, and enjoy the chaos.
                </p>
            </section>
            <section>
                <p>
                    We’re here to build a strong, supportive, and hilarious community of players who thrive in all corners of the ’verse — from mining rocks and hauling cargo to jumping into firefights and forgetting to bring medpens.
                </p>
                <p>
                    We believe in teamwork without pressure, structure without rigidity, and fun above all else. Through our three divisions — Logistics, Industry, and Security — we aim to create opportunities for every member to find their place, chase their goals, and make some questionable decisions with good company.
                </p>
            </section>

            <section>
                <p class="font-bold text-amber-300">Because in the end, space is cold — but it doesn’t have to be lonely.</p>
            </section>
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
