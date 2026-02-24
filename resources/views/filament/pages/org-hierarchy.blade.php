<x-filament-panels::page>

    {{-- ─────────────────────── LEADERSHIP ─────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-8 w-1 rounded bg-yellow-400"></div>
            <h2 class="text-xl font-bold tracking-widest uppercase text-yellow-400 dark:text-yellow-300">
                Leadership
            </h2>
        </div>

        @php $leaders = $this->getLeaders() @endphp

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
                            <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $leader->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $leader->handle }}</p>
                            @if($leader->title)
                                <span class="mt-1 inline-block text-xs font-medium text-yellow-600 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/30 px-2 py-0.5 rounded">
                                    {{ $leader->title }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ─────────────────────── UNITS ─────────────────────── --}}
    @php
        $units = $this->getUnits();
        $unitStyles = [
            'Security' => ['border' => 'border-red-500/40',   'bg' => 'bg-red-500/5',   'accent' => 'bg-red-500',   'text' => 'text-red-400',   'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
            'Industry' => ['border' => 'border-amber-500/40', 'bg' => 'bg-amber-500/5', 'accent' => 'bg-amber-500', 'text' => 'text-amber-400', 'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
            'Racing'   => ['border' => 'border-green-500/40', 'bg' => 'bg-green-500/5', 'accent' => 'bg-green-500', 'text' => 'text-green-400', 'badge' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        ];
    @endphp

    <div class="space-y-8">
        @foreach($units as $unit)
            @php $s = $unitStyles[$unit->name] ?? ['border'=>'border-blue-500/40','bg'=>'bg-blue-500/5','accent'=>'bg-blue-500','text'=>'text-blue-400','badge'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'] @endphp

            <div class="rounded-2xl border {{ $s['border'] }} {{ $s['bg'] }} p-5 shadow-sm">

                {{-- Unit header --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-8 w-1 rounded {{ $s['accent'] }}"></div>
                    <h3 class="text-lg font-bold tracking-widest uppercase {{ $s['text'] }}">
                        {{ $unit->name }} Division
                    </h3>
                    <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">
                        {{ $unit->teams->count() }} {{ str()->plural('team', $unit->teams->count()) }}
                    </span>
                </div>

                @if($unit->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 pl-4">{{ $unit->description }}</p>
                @endif

                {{-- Teams --}}
                @if($unit->teams->isEmpty())
                    <p class="text-sm text-gray-400 italic pl-4">No teams in this unit yet.</p>
                @else
                    <div class="space-y-4 pl-4">
                        @foreach($unit->teams as $team)
                            <div class="rounded-xl border border-white/10 bg-white/5 dark:bg-gray-800/50 p-4">

                                {{-- Team header --}}
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $team->teamMembers->count() }} {{ str()->plural('member', $team->teamMembers->count()) }}
                                    </span>
                                </div>

                                @if($team->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $team->description }}</p>
                                @endif

                                @if($team->teamMembers->isEmpty())
                                    <p class="text-xs text-gray-400 italic">No members assigned.</p>
                                @else
                                    {{-- Leaders first --}}
                                    @php
                                        $teamLeaders  = $team->teamMembers->where('role', 'leader');
                                        $teamRegulars = $team->teamMembers->where('role', 'member');
                                    @endphp

                                    @if($teamLeaders->isNotEmpty())
                                        <div class="mb-2">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Leaders</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($teamLeaders as $tm)
                                                    <div class="flex items-center gap-2 rounded-lg border {{ $s['border'] }} {{ $s['bg'] }} px-3 py-1.5">
                                                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $s['accent'] }}/20 {{ $s['text'] }} text-xs font-bold">
                                                            {{ strtoupper(substr($tm->member->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $tm->member->name }}</span>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ $tm->member->handle }}</span>
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
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Members</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($teamRegulars as $tm)
                                                    <div class="flex items-center gap-2 rounded-lg bg-white/5 dark:bg-gray-700/50 border border-white/10 px-3 py-1.5">
                                                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-bold">
                                                            {{ strtoupper(substr($tm->member->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <span class="text-sm text-gray-900 dark:text-white">{{ $tm->member->name }}</span>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ $tm->member->handle }}</span>
                                                            @if($tm->title)
                                                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400 italic">{{ $tm->title }}</span>
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
            </div>
        @endforeach
    </div>

</x-filament-panels::page>
