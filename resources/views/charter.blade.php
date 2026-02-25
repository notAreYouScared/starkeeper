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
                    <span class="text-xl font-bold tracking-wider text-blue-400">StarKeeper</span>
                </a>
                <span class="text-gray-500">/</span>
                <span class="text-sm text-gray-300">Charter</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('org-hierarchy') }}" class="text-gray-300 hover:text-blue-400 transition-colors">Org Hierarchy</a>
                <a href="{{ route('history') }}" class="text-gray-300 hover:text-blue-400 transition-colors">History</a>
                <a href="{{ route('manifesto') }}" class="text-gray-300 hover:text-blue-400 transition-colors">Manifesto</a>
                <a href="{{ route('charter') }}" class="text-purple-400">Charter</a>
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
            <div class="h-10 w-1 rounded bg-purple-400"></div>
            <h1 class="text-3xl font-black tracking-widest uppercase text-purple-400">Charter</h1>
        </div>

        <div class="space-y-8 text-gray-300 leading-relaxed">

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">I. Membership</h2>
                <ol class="space-y-2 list-decimal list-inside">
                    <li>Membership in StarKeeper is open to any Star Citizen player in good standing.</li>
                    <li>All new members are subject to a probationary period during which leadership will evaluate
                        conduct and contribution before granting full member status.</li>
                    <li>Members are expected to conduct themselves with respect and professionalism at all times,
                        both within the organisation and when representing StarKeeper in public.</li>
                    <li>Membership may be revoked by leadership for conduct unbecoming, repeated rule violations,
                        or prolonged inactivity without notice.</li>
                </ol>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">II. Code of Conduct</h2>
                <ol class="space-y-2 list-decimal list-inside">
                    <li>Treat every member and guest with respect. Harassment, discrimination, and bullying of any kind
                        will not be tolerated and may result in immediate removal.</li>
                    <li>Do not engage in griefing, scamming, or any activity that brings disrepute to the StarKeeper name.</li>
                    <li>Disputes between members are to be resolved peacefully; if resolution cannot be reached,
                        the matter is to be escalated to a Director or leadership representative.</li>
                    <li>Keep organisational communications (strategies, rosters, internal discussions) confidential
                        unless explicitly authorised to share.</li>
                    <li>Do not make public statements on behalf of StarKeeper without prior approval from leadership.</li>
                </ol>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">III. Organisational Structure</h2>
                <ol class="space-y-2 list-decimal list-inside">
                    <li><strong class="text-white">Leadership</strong> — The founding council and head officers responsible
                        for overall direction, policy, and final decisions.</li>
                    <li><strong class="text-white">Directors</strong> — Division leads who manage day-to-day operations
                        within Security, Industry, and Racing.</li>
                    <li><strong class="text-white">Moderators</strong> — Trusted members who assist with community
                        management, event coordination, and member support.</li>
                    <li><strong class="text-white">Members</strong> — The core of StarKeeper, participating in operations
                        and contributing to org activities.</li>
                    <li>Promotions are awarded based on demonstrated contribution, reliability, and the recommendation
                        of a Director or Leadership member.</li>
                </ol>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">IV. Operations</h2>
                <ol class="space-y-2 list-decimal list-inside">
                    <li>Scheduled operations are announced in advance via Discord and the org website.
                        Members are encouraged to attend but attendance is never mandatory.</li>
                    <li>During operations members are expected to follow the directions of the designated
                        Operation Commander unless those directions violate this Charter or common sense.</li>
                    <li>Loot, credits, and rewards obtained during joint operations are distributed according to
                        the policy set by the Operation Commander before the operation begins.</li>
                    <li>Friendly fire is strictly prohibited during operations and may be treated as a
                        conduct violation.</li>
                </ol>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">V. Amendments</h2>
                <ol class="space-y-2 list-decimal list-inside">
                    <li>This Charter may be amended by a majority vote of the Leadership council.</li>
                    <li>Proposed amendments must be circulated to all Directors at least 72 hours before a vote
                        is called.</li>
                    <li>All members will be notified of Charter changes within 24 hours of ratification.</li>
                </ol>
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
