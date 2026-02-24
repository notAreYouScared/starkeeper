<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organisation Hierarchy – StarKeeper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
        document.documentElement.classList.add('dark');
    </script>
</head>
<body class="min-h-full bg-gray-950 text-gray-100 antialiased">

    {{-- ─── Header ─── --}}
    <header class="border-b border-white/10 bg-gray-900/80 backdrop-blur sticky top-0 z-10">
        <div class="mx-auto max-w-5xl px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold tracking-wider text-blue-400">StarKeeper</span>
                <span class="text-gray-500">/</span>
                <span class="text-sm text-gray-300">Organisation Hierarchy</span>
            </div>
            @auth
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                   class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                    Admin Panel →
                </a>
            @else
                <a href="{{ route('filament.admin.auth.login') }}"
                   class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                    Sign in →
                </a>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 space-y-10">

        {{-- ─────────────────────── LEADERSHIP ─────────────────────── --}}
        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="h-8 w-1 rounded bg-yellow-400"></div>
                <h2 class="text-xl font-bold tracking-widest uppercase text-yellow-400">
                    Leadership
                </h2>
            </div>

            @if($leaders->isEmpty())
                <p class="text-sm text-gray-400 italic pl-4">No leadership members assigned yet.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pl-4">
                    @foreach($leaders as $leader)
                        <div class="flex items-center gap-3 rounded-xl border border-yellow-400/30 bg-yellow-400/5 px-4 py-3 shadow-sm">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-yellow-400/20 text-yellow-400 font-bold text-sm">
                                {{ strtoupper(substr($leader->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-white truncate">{{ $leader->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $leader->handle }}</p>
                                @if($leader->title)
                                    <span class="mt-1 inline-block text-xs font-medium text-yellow-300 bg-yellow-900/30 px-2 py-0.5 rounded">
                                        {{ $leader->title }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ─────────────────────── UNITS ─────────────────────── --}}
        @php
            $unitStyles = [
                'Security' => ['border' => 'border-red-500/40',   'bg' => 'bg-red-500/5',   'accent' => 'bg-red-500',   'text' => 'text-red-400',   'badge' => 'bg-red-900/30 text-red-300',   'avatarBg' => 'bg-red-500/20'],
                'Industry' => ['border' => 'border-amber-500/40', 'bg' => 'bg-amber-500/5', 'accent' => 'bg-amber-500', 'text' => 'text-amber-400', 'badge' => 'bg-amber-900/30 text-amber-300', 'avatarBg' => 'bg-amber-500/20'],
                'Racing'   => ['border' => 'border-green-500/40', 'bg' => 'bg-green-500/5', 'accent' => 'bg-green-500', 'text' => 'text-green-400', 'badge' => 'bg-green-900/30 text-green-300', 'avatarBg' => 'bg-green-500/20'],
            ];
        @endphp

        <div class="space-y-8">
            @foreach($units as $unit)
                @php $s = $unitStyles[$unit->name] ?? ['border'=>'border-blue-500/40','bg'=>'bg-blue-500/5','accent'=>'bg-blue-500','text'=>'text-blue-400','badge'=>'bg-blue-900/30 text-blue-300','avatarBg'=>'bg-blue-500/20'] @endphp

                <section class="rounded-2xl border {{ $s['border'] }} {{ $s['bg'] }} p-5 shadow-sm">

                    {{-- Unit header --}}
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-8 w-1 rounded {{ $s['accent'] }}"></div>
                        <h3 class="text-lg font-bold tracking-widest uppercase {{ $s['text'] }}">
                            {{ $unit->name }} Division
                        </h3>
                        <span class="ml-auto text-xs text-gray-500">
                            {{ $unit->teams->count() }} {{ str()->plural('team', $unit->teams->count()) }}
                        </span>
                    </div>

                    @if($unit->description)
                        <p class="text-sm text-gray-400 mb-4 pl-4">{{ $unit->description }}</p>
                    @endif

                    {{-- Teams --}}
                    @if($unit->teams->isEmpty())
                        <p class="text-sm text-gray-400 italic pl-4">No teams in this unit yet.</p>
                    @else
                        <div class="space-y-4 pl-4">
                            @foreach($unit->teams as $team)
                                <div class="rounded-xl border border-white/10 bg-gray-800/50 p-4">

                                    {{-- Team header --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-white">{{ $team->name }}</h4>
                                        <span class="text-xs text-gray-500">
                                            {{ $team->teamMembers->count() }} {{ str()->plural('member', $team->teamMembers->count()) }}
                                        </span>
                                    </div>

                                    @if($team->description)
                                        <p class="text-xs text-gray-400 mb-3">{{ $team->description }}</p>
                                    @endif

                                    @if($team->teamMembers->isEmpty())
                                        <p class="text-xs text-gray-400 italic">No members assigned.</p>
                                    @else
                                        @php
                                            $teamLeaders  = $team->teamMembers->where('role', 'leader');
                                            $teamRegulars = $team->teamMembers->where('role', 'member');
                                        @endphp

                                        @if($teamLeaders->isNotEmpty())
                                            <div class="mb-2">
                                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Leaders</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($teamLeaders as $tm)
                                                        <div class="flex items-center gap-2 rounded-lg border {{ $s['border'] }} {{ $s['bg'] }} px-3 py-1.5">
                                                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $s['avatarBg'] }} {{ $s['text'] }} text-xs font-bold">
                                                                {{ strtoupper(substr($tm->member->name, 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <span class="text-sm font-medium text-white">{{ $tm->member->name }}</span>
                                                                <span class="text-xs text-gray-400 ml-1">{{ $tm->member->handle }}</span>
                                                                @if($tm->title)
                                                                    <span class="ml-1 text-xs font-medium {{ $s['badge'] }} px-1.5 py-0.5 rounded">{{ $tm->title }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($teamRegulars->isNotEmpty())
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Members</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($teamRegulars as $tm)
                                                        <div class="flex items-center gap-2 rounded-lg bg-gray-700/50 border border-white/10 px-3 py-1.5">
                                                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-600 text-gray-300 text-xs font-bold">
                                                                {{ strtoupper(substr($tm->member->name, 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <span class="text-sm text-white">{{ $tm->member->name }}</span>
                                                                <span class="text-xs text-gray-400 ml-1">{{ $tm->member->handle }}</span>
                                                                @if($tm->title)
                                                                    <span class="ml-1 text-xs text-gray-400 italic">{{ $tm->title }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </div>

    </main>

</body>
</html>
