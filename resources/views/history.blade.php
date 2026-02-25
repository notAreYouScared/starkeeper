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
                    <img src="{{ url('image/logo.png') }}"
                         alt="StarKeeper Logo"
                         class="h-8 w-auto">
                    <span class="text-xl font-bold tracking-wider text-blue-400">Starkeeper Industries</span>
                </a>
                <span class="text-gray-500">/</span>
                <span class="text-sm text-gray-300">History</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('home') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Home</a>
                <a href="{{ route('org-hierarchy') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Org Hierarchy</a>
                <a href="{{ route('history') }}"
                   class="text-yellow-400">History</a>
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

    <main class="mx-auto max-w-3xl px-4 py-12">

        <div class="flex items-center gap-3 mb-8">
            <div class="h-10 w-1 rounded bg-yellow-400"></div>
            <h1 class="text-3xl font-black tracking-widest uppercase text-yellow-400">History - PAGE IS WIP</h1>
        </div>

        <div class="space-y-6 text-gray-300 leading-relaxed">

            <p>
                Looking for an org that’s chill, semi-functional, and slightly obsessed with loot? You found us.
                Starkeeper Industries is a US-based, laid-back org with no activity requirements, weekly events, and a proud tradition of barely holding it together and still winning.
                We’re a bunch of casual misfits who like to run missions, talk trash, and make a mess of the ‘verse—with just enough structure to keep things from catching fire. (Well, most of the time.)
            </p>

            <p>
                Starkeeper Industries is built around good people, chill vibes, and a healthy dose of space chaos. We welcome everyone from veteran players to brand-new pilots and encourage members to jump in, squad up, and have fun. Whether you’re moving cargo, mining ore, running escort, or just causing “accidental” explosions, there’s a place for you here.

                There are no activity requirements — life comes first. We’re a dysfunctional family in the best way: supportive, sarcastic, and always down for a good time (or at least a decent crash landing).
                If you’re looking for an org that’s active without being sweaty, organized without being strict, and full of people who laugh when things go wrong… you’re in the right place.
            </p>

            <p>
                Because space is dangerous, weird, and way more fun with people who don’t mind laughing through the chaos.

                Here’s what you get when you join us:

                Laid-Back Vibes – No activity requirements, no pressure. Play when you want, how you want. We’re here to have fun, not run a second job.
                Three Divisions, One Dysfunctional Family – Whether you’re into hauling cargo, building industry, or bringing the boom, we’ve got a place for you in Logistics, Industry, or Security.
                Weekly Events – We run casual ops every week (usually around 9pm EST) with a mix of PvE, PvP, and “did anyone remember to fuel the ship?” moments.
                Active Discord – Our comms are always buzzing. Voice chat, memes, tactical talk, or just yelling about someone flying their Cutty Black into a wall — we’ve got it all.
                Welcoming Crew – New players, returning vets, and day-one grinders all welcome. We’ve got your back (and probably a medpen).
                If you’re looking for a crew that knows how to get stuff done and have a laugh doing it, Starkeeper Industries is the org for you.

                Join us. It’ll be fine… Probably.
            </p>

            <p>
                We run three glorious divisions, each with its own vibe, ops, and opportunities to make bad decisions with good people:

                Industry – You like rocks? We got rocks. Mining, refining, salvaging—if it makes money, we’re on it.
                Logistics – Space Uber meets Amazon Prime. We haul, trade, and deliver with minimal explosions.
                Security – Big guns. Loud noises. PvP. Bunker ops. Escort missions. We shoot first and ask “was that a teammate?” later.
                Play your way, switch branches anytime, and never worry about being “good enough.” If you’re breathing and semi-coherent, you’re qualified.
            </p>

            <p>
                Still Not Sure?
                Hop into a mission and vibe with us. Worst case, you get shot in the back by someone friendly.
                Best case? You find your new favorite place to blow off steam (and maybe some ships).
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
