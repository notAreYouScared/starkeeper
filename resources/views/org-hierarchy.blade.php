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

        {{-- ─────────────────────── ORG ROLES ─────────────────────── --}}
        @php
            $roleAccents = [
                'leadership' => ['bar' => 'bg-yellow-400', 'text' => 'text-yellow-400', 'border' => 'border-yellow-400/30', 'bg' => 'bg-yellow-400/5', 'avatar' => 'bg-yellow-400/20 text-yellow-400'],
                'director'   => ['bar' => 'bg-blue-400',   'text' => 'text-blue-400',   'border' => 'border-blue-400/30',   'bg' => 'bg-blue-400/5',   'avatar' => 'bg-blue-400/20 text-blue-400'],
                'mod'        => ['bar' => 'bg-green-400',  'text' => 'text-green-400',  'border' => 'border-green-400/30',  'bg' => 'bg-green-400/5',  'avatar' => 'bg-green-400/20 text-green-400'],
            ];
            $defaultAccent = ['bar' => 'bg-gray-400', 'text' => 'text-gray-300', 'border' => 'border-white/10', 'bg' => 'bg-white/5', 'avatar' => 'bg-gray-600 text-gray-300'];
        @endphp

        @foreach($membersByRole as $group)
            @php $accent = $roleAccents[$group['role']->name] ?? $defaultAccent @endphp
            <section>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-8 w-1 rounded {{ $accent['bar'] }}"></div>
                    <h2 class="text-xl font-bold tracking-widest uppercase {{ $accent['text'] }}">
                        {{ $group['role']->label }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pl-4">
                    @foreach($group['members'] as $member)
                        <div class="flex items-center gap-3 rounded-xl border {{ $accent['border'] }} {{ $accent['bg'] }} px-4 py-3 shadow-sm">
                            @if($member->avatar_url)
                                <img src="{{ $member->avatar_url }}"
                                     alt="{{ $member->name }}"
                                     class="h-10 w-10 shrink-0 rounded-full object-cover">
                            @else
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $accent['avatar'] }} font-bold text-sm">
                                    {{ strtoupper(substr($member->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                @if($member->profile_url)
                                    <a href="{{ $member->profile_url }}" target="_blank" rel="noopener noreferrer"
                                       class="font-semibold text-white truncate hover:underline">{{ $member->name }}</a>
                                @else
                                    <p class="font-semibold text-white truncate">{{ $member->name }}</p>
                                @endif
                                @if($member->title)
                                    <span class="mt-1 inline-block text-xs font-medium {{ $accent['text'] }} bg-white/5 px-2 py-0.5 rounded">
                                        {{ $member->title }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        {{-- ─────────────────────── UNITS ─────────────────────── --}}
        @php
            $unitStyles = [
                'Security' => ['border' => 'border-red-500/40',   'bg' => 'bg-red-500/5',   'accent' => 'bg-red-500',   'text' => 'text-red-400',   'badge' => 'bg-red-900/30 text-red-300',   'avatarBg' => 'bg-red-500/20'],
                'Industry' => ['border' => 'border-green-500/40', 'bg' => 'bg-green-500/5', 'accent' => 'bg-green-500', 'text' => 'text-green-400', 'badge' => 'bg-green-900/30 text-green-300', 'avatarBg' => 'bg-green-500/20'],
                'Racing'   => ['border' => 'border-blue-500/40', 'bg' => 'bg-blue-500/5', 'accent' => 'bg-blue-500', 'text' => 'text-blue-400', 'badge' => 'bg-blue-900/30 text-blue-300', 'avatarBg' => 'bg-blue-500/20'],
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
                                    <div class="flex items-start gap-4 mb-3">
                                        @if($team->image)
                                            <img src="{{ Storage::disk('public')->url($team->image) }}"
                                                 alt="{{ $team->name }} patch"
                                                 class="h-36 w-36 shrink-0 rounded-lg p-1">
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-semibold text-white">{{ $team->name }}</h4>
                                                <span class="text-xs text-gray-500 ml-2 shrink-0">
                                                    {{ $team->teamMembers->count() }} {{ str()->plural('member', $team->teamMembers->count()) }}
                                                </span>
                                            </div>
                                            @if($team->description)
                                                <p class="text-xs text-gray-400 mt-1">{{ $team->description }}</p>
                                            @endif
                                        </div>
                                    </div>

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
                                                            @if($tm->member->avatar_url)
                                                                <img src="{{ $tm->member->avatar_url }}"
                                                                     alt="{{ $tm->member->name }}"
                                                                     class="h-6 w-6 shrink-0 rounded-full object-cover">
                                                            @else
                                                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $s['avatarBg'] }} {{ $s['text'] }} text-xs font-bold">
                                                                    {{ strtoupper(substr($tm->member->name, 0, 2)) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <span class="text-sm font-medium text-white">{{ $tm->member->name }}</span>
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
                                                            @if($tm->member->avatar_url)
                                                                <img src="{{ $tm->member->avatar_url }}"
                                                                     alt="{{ $tm->member->name }}"
                                                                     class="h-6 w-6 shrink-0 rounded-full object-cover">
                                                            @else
                                                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-600 text-gray-300 text-xs font-bold">
                                                                    {{ strtoupper(substr($tm->member->name, 0, 2)) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <span class="text-sm text-white">{{ $tm->member->name }}</span>
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

