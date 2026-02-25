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
                    <img src="https://robertsspaceindustries.com/media/wpx3j3876pn7ir/logo/STARKEEPER-Logo.png"
                         alt="StarKeeper Logo"
                         class="h-8 w-auto">
                    <span class="text-xl font-bold tracking-wider text-blue-400">StarKeeper</span>
                </a>
                <span class="text-gray-500">/</span>
                <span class="text-sm text-gray-300">Manifesto</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('org-hierarchy') }}" class="text-gray-300 hover:text-blue-400 transition-colors">Org Hierarchy</a>
                <a href="{{ route('history') }}" class="text-gray-300 hover:text-blue-400 transition-colors">History</a>
                <a href="{{ route('manifesto') }}" class="text-green-400">Manifesto</a>
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
            <div class="h-10 w-1 rounded bg-green-400"></div>
            <h1 class="text-3xl font-black tracking-widest uppercase text-green-400">Manifesto</h1>
        </div>

        <div class="space-y-8 text-gray-300 leading-relaxed">

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">Who We Are</h2>
                <p>
                    StarKeeper is a multi-role Star Citizen organisation built on the foundations of camaraderie,
                    professionalism, and a relentless drive to push further into the unknown.
                    We are soldiers, traders, miners, racers, and explorers—united under a single banner
                    and dedicated to excellence in everything we do.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">Our Values</h2>
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-green-400"></span>
                        <span><strong class="text-white">Loyalty.</strong> We stand by our members in good times and bad.
                        The crew comes first—always.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-green-400"></span>
                        <span><strong class="text-white">Integrity.</strong> We are honest with each other and with those
                        we do business with. Our word is our bond across every system.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-green-400"></span>
                        <span><strong class="text-white">Excellence.</strong> We strive to master our chosen roles,
                        share knowledge freely, and raise the skill of every member around us.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-green-400"></span>
                        <span><strong class="text-white">Inclusivity.</strong> The 'verse is vast and diverse—so are we.
                        Every background, playstyle, and time zone is welcome here.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-green-400"></span>
                        <span><strong class="text-white">Fun.</strong> At the end of the day this is a game, and we are
                        here to enjoy it. We take our commitments seriously without losing sight of that.</span>
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">Our Mission</h2>
                <p>
                    To build and maintain a thriving, organised community where every member has a meaningful role to play—
                    whether securing the trade lanes, extracting resources from distant asteroids, tearing up the racing circuit,
                    or pushing the frontier of human exploration.
                    We aim to be an organisation that others look to as a model of what a well-run, member-first crew looks like.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-white mb-3 uppercase tracking-wider">Our Commitment to You</h2>
                <p>
                    As a member of StarKeeper you will always have a fleet to fly with, a team to support you,
                    and leadership that listens.
                    We invest in our members—through training, resources, and opportunity—because we know that
                    a stronger individual makes a stronger organisation.
                    We promise to treat every member with respect, to be transparent in our decisions, and to keep
                    the culture of StarKeeper one worth being part of.
                </p>
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
