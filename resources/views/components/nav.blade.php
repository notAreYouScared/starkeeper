@props(['activePage' => null, 'breadcrumb' => null])

@php
    $navLinks = [
        'home'          => ['route' => 'home',          'label' => 'Home'],
        'hierarchy'     => ['route' => 'hierarchy', 'label' => 'Hierarchy'],
        'history'       => ['route' => 'history',       'label' => 'History'],
        'manifesto'     => ['route' => 'manifesto',     'label' => 'Manifesto'],
        'charter'       => ['route' => 'charter',       'label' => 'Charter'],
    ];
@endphp

<header class="border-b border-white/10 bg-gray-900/80 backdrop-blur sticky top-0 z-10">
    <div class="mx-auto max-w-5xl px-4 py-4">
        <div class="flex items-center justify-between">
            {{-- ─── Logo / Breadcrumb ─── --}}
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity shrink-0">
                    <img src="{{ url('image/logo.png') }}"
                         alt="StarKeeper Logo"
                         class="h-8 w-auto">
                    <span class="text-xl font-bold tracking-wider text-blue-400 hidden sm:inline">Starkeeper Industries</span>
                </a>
                @if($breadcrumb)
                    <span class="text-gray-500 hidden sm:inline">/</span>
                    <span class="text-sm text-gray-300 truncate hidden sm:inline">{{ $breadcrumb }}</span>
                @endif
            </div>

            {{-- ─── Desktop Nav ─── --}}
            <nav class="hidden md:flex items-center gap-4 text-sm">
                @foreach($navLinks as $key => $link)
                    <a href="{{ route($link['route']) }}"
                       class="{{ $activePage === $key ? 'text-blue-400' : 'text-gray-300 hover:text-blue-400 transition-colors' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
                @auth
                    @if($myMember ?? null)
                        <a href="{{ route('member.profile', $myMember) }}"
                           class="text-gray-300 hover:text-blue-400 transition-colors">My Profile</a>
                    @endif
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                       class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Admin Panel →</a>
                @else
                    <a href="{{ route('filament.admin.auth.login') }}"
                       class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Sign in →</a>
                @endauth
            </nav>

            {{-- ─── Mobile Hamburger ─── --}}
            <button id="nav-toggle"
                    aria-label="Toggle navigation"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    class="md:hidden flex flex-col justify-center items-center w-10 h-10 rounded-lg hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400">
                <span class="block w-5 h-0.5 bg-gray-300 transition-transform duration-200" id="hamburger-top"></span>
                <span class="block w-5 h-0.5 bg-gray-300 my-1 transition-opacity duration-200" id="hamburger-mid"></span>
                <span class="block w-5 h-0.5 bg-gray-300 transition-transform duration-200" id="hamburger-bot"></span>
            </button>
        </div>

        {{-- ─── Mobile Menu ─── --}}
        <nav id="mobile-menu"
             class="md:hidden hidden gap-1 pt-3 pb-2 text-sm border-t border-white/10 mt-3"
             aria-label="Mobile navigation">
            @foreach($navLinks as $key => $link)
                <a href="{{ route($link['route']) }}"
                   class="px-3 py-2 rounded-lg {{ $activePage === $key ? 'text-blue-400 bg-blue-400/10' : 'text-gray-300 hover:text-blue-400 hover:bg-white/5 transition-colors' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                @if($myMember ?? null)
                    <a href="{{ route('member.profile', $myMember) }}"
                       class="px-3 py-2 rounded-lg text-gray-300 hover:text-blue-400 hover:bg-white/5 transition-colors">My Profile</a>
                @endif
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                   class="px-3 py-2 rounded-lg text-blue-400 hover:text-blue-300 hover:bg-white/5 transition-colors">Admin Panel →</a>
            @else
                <a href="{{ route('filament.admin.auth.login') }}"
                   class="px-3 py-2 rounded-lg text-blue-400 hover:text-blue-300 hover:bg-white/5 transition-colors">Sign in →</a>
            @endauth
        </nav>
    </div>
</header>

<script>
(function () {
    var btn = document.getElementById('nav-toggle');
    var menu = document.getElementById('mobile-menu');
    var top = document.getElementById('hamburger-top');
    var mid = document.getElementById('hamburger-mid');
    var bot = document.getElementById('hamburger-bot');

    btn.addEventListener('click', function () {
        var isNowHidden = menu.classList.toggle('hidden');
        menu.classList.toggle('flex', !isNowHidden);
        menu.classList.toggle('flex-col', !isNowHidden);
        btn.setAttribute('aria-expanded', String(!isNowHidden));
        top.style.transform = isNowHidden ? '' : 'translateY(6px) rotate(45deg)';
        mid.style.opacity   = isNowHidden ? '' : '0';
        bot.style.transform = isNowHidden ? '' : 'translateY(-6px) rotate(-45deg)';
    });
})();
</script>
