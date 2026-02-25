<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charter – StarKeeper</title>
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
                <span class="text-sm text-gray-300">Charter</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('home') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Home</a>
                <a href="{{ route('org-hierarchy') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Org Hierarchy</a>
                <a href="{{ route('history') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">History</a>
                <a href="{{ route('manifesto') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Manifesto</a>
                <a href="{{ route('charter') }}"
                   class="text-purple-400">Charter</a>
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
            <div class="h-10 w-1 rounded bg-purple-400"></div>
            <h1 class="text-3xl font-black tracking-widest uppercase text-purple-400">Charter</h1>
        </div>

        <div class="space-y-8 text-gray-300 leading-relaxed">

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">I. Purpose</h2>
                Starkeeper Industries exists to bring together explorers, fighters, traders, and all-around weirdos from across the ‘verse in one laid-back, often chaotic but oddly effective organization.
                Whether you’re hauling cargo, mining rocks, or storming bunkers, there’s a place for you here.
                Our goal? Have fun, blow stuff up (on purpose or not), and look good doing it.
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">II. Structure</h2>
                <ol class="space-y-2 list-disc list-inside">
                    <li>
                        <strong class="text-white">Industry</strong> – Mining, refining, repairing, salvaging—basically the folks who make the money and fix the stuff we break.
                    </li>
                    <li>
                        <strong class="text-white">Logistics</strong> – Hauling gear, moving assets, organizing supply lines, and always forgetting where they parked.
                    </li>
                    <li>
                        <strong class="text-white">Security</strong> – If it shoots, they bring it. From bunker clearing to escort missions, these are the trigger-happy pros (or close enough).
                    </li>
                </ol>
                <br/>
                Each branch is autonomous but works together like a well-lubed multicrew ship… on a good day.
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">III. Membership</h2>
                <ol class="space-y-2 list-disc list-inside">
                    <li>
                        Say hi.
                    </li>
                    <li>
                        Don't be a jerk.
                    </li>
                    <li>
                        Pick a Branch ( or don't-wander the void!).
                    </li>
                    <li>
                        Participate when you want; we don't do mandatory attendance.
                    </li>
                </ol>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">IV. Culture</h2>
                <ol class="space-y-2 list-disc list-inside">
                    <li>We are casual-first, fun-second, professional-never (unless we really have to).</li>
                    <li>Weekly events usually start at 7 or 9 PM EST—pop in or out as you like.</li>
                    <li>We’re a dysfunctional family in the best way: chaotic, loyal, and always ready to revive you when you faceplant in a bunker.</li>
                </ol>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">V. Rules</h2>
                <ol class="space-y-2 list-disc list-inside">
                    <li>We joke a lot, but we take respect and inclusion seriously.</li>
                    <li>No harassment, hate speech, or griefing. We don’t tolerate that.</li>
                    <li>Follow leadership guidance, especially in missions.</li>
                    <li>Most importantly: bring snacks. Or ammo. Preferably both.</li>
                </ol>
            </section>

            <br>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">V. Leadership</h2>
                Leadership exists to guide the chaos, not crush it. Branch leads and team leads help coordinate events, answer questions, and keep things moving.
            <br><br>
                Think of them less like bosses and more like your slightly responsible space uncles and aunts.
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
