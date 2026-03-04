<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hierarchy – StarKeeper</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full bg-gray-950 text-gray-100 antialiased">

    <x-nav active-page="hierarchy" breadcrumb="Hierarchy" />

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
                            <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-white truncate">{{ $member->name }}</p>
                                @if($member->title)
                                    <span class="mt-1 inline-block text-xs font-medium {{ $accent['text'] }} bg-white/5 px-2 py-0.5 rounded">
                                        {{ $member->title }}
                                    </span>
                                @endif
                            </div>
                            @auth
                                <a href="{{ route('member.profile', $member) }}"
                                   class="ml-auto shrink-0 text-gray-500 hover:text-blue-400 transition-colors"
                                   aria-label="View {{ $member->name }}'s profile">
                                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            @endauth
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
                                            $byRole = $team->teamMembers->groupBy(fn ($tm) => $tm->teamRole?->id ?? 0);
                                            $sortedRoles = $team->teamMembers
                                                ->sortBy(fn ($tm) => $tm->teamRole?->sort_order ?? 9999)
                                                ->map(fn ($tm) => [
                                                    'key'   => $tm->teamRole?->id ?? 0,
                                                    'label' => $tm->teamRole?->label ?? 'Unassigned',
                                                    'color' => $tm->teamRole?->color,
                                                ])
                                                ->unique('key')
                                                ->values();
                                            // Hex alpha suffixes: 66 = 40%, 33 = 20%, 26 = 15%, 0d = 5%
                                        @endphp

                                        @foreach($sortedRoles as $roleEntry)
                                            @php
                                                $roleMembers = $byRole[$roleEntry['key']] ?? collect();
                                                $roleColor   = $roleEntry['color'];
                                            @endphp
                                            @if($roleMembers->isNotEmpty())
                                                <div class="mb-2">
                                                    <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                                                       @if($roleColor) style="color: {{ $roleColor }}" @else class="text-gray-500" @endif>
                                                        {{ $roleEntry['label'] }}
                                                    </p>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($roleMembers as $tm)
                                                            <div class="flex items-center gap-2 rounded-lg border px-3 py-1.5"
                                                                 @if($roleColor)
                                                                     style="border-color: {{ $roleColor }}66; background-color: {{ $roleColor }}0d"
                                                                 @else
                                                                     style="border-color: rgba(255,255,255,0.1); background-color: rgba(255,255,255,0.03)"
                                                                 @endif>
                                                                @if($tm->member->avatar_url)
                                                                    <img src="{{ $tm->member->avatar_url }}"
                                                                         alt="{{ $tm->member->name }}"
                                                                         class="h-6 w-6 shrink-0 rounded-full object-cover">
                                                                @else
                                                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                                                         @if($roleColor)
                                                                             style="background-color: {{ $roleColor }}33; color: {{ $roleColor }}"
                                                                         @else
                                                                             style="background-color: rgba(75,85,99,0.5); color: rgb(209,213,219)"
                                                                         @endif>
                                                                        {{ strtoupper(substr($tm->member->name, 0, 2)) }}
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    @auth
                                                                        <a href="{{ route('member.profile', $tm->member) }}"
                                                                           class="text-sm font-medium text-white hover:text-blue-400 transition-colors">{{ $tm->member->name }}</a>
                                                                    @else
                                                                        <span class="text-sm font-medium text-white">{{ $tm->member->name }}</span>
                                                                    @endauth
                                                                    @if($tm->title)
                                                                        <span class="ml-1 text-xs font-medium px-1.5 py-0.5 rounded"
                                                                              @if($roleColor)
                                                                                  style="background-color: {{ $roleColor }}26; color: {{ $roleColor }}"
                                                                              @else
                                                                                  style="background-color: rgba(75,85,99,0.3); color: rgb(209,213,219)"
                                                                              @endif>{{ $tm->title }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </div>

    </main>

    <x-footer />

</body>
</html>

