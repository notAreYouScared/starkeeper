<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $member->name }} – StarKeeper</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full bg-gray-950 text-gray-100 antialiased">

    <x-nav :breadcrumb="$member->name" />

    <main class="mx-auto max-w-5xl px-4 py-10 space-y-8">

        @php
            $roleAccents = [
                'leadership' => ['bar' => 'bg-yellow-400', 'text' => 'text-yellow-400', 'border' => 'border-yellow-400/30', 'bg' => 'bg-yellow-400/5', 'avatar' => 'bg-yellow-400/20 text-yellow-400'],
                'director'   => ['bar' => 'bg-blue-400',   'text' => 'text-blue-400',   'border' => 'border-blue-400/30',   'bg' => 'bg-blue-400/5',   'avatar' => 'bg-blue-400/20 text-blue-400'],
                'mod'        => ['bar' => 'bg-green-400',  'text' => 'text-green-400',  'border' => 'border-green-400/30',  'bg' => 'bg-green-400/5',  'avatar' => 'bg-green-400/20 text-green-400'],
            ];
            $defaultAccent = ['bar' => 'bg-gray-400', 'text' => 'text-gray-300', 'border' => 'border-white/10', 'bg' => 'bg-white/5', 'avatar' => 'bg-gray-600 text-gray-300'];
            $accent = $roleAccents[$member->orgRole?->name ?? ''] ?? $defaultAccent;
        @endphp

        {{-- ─── Member Card ─── --}}
        <section class="rounded-2xl border {{ $accent['border'] }} {{ $accent['bg'] }} p-6">
            <div class="flex items-center gap-5">
                @if($member->avatar_url)
                    <img src="{{ $member->avatar_url }}"
                         alt="{{ $member->name }}"
                         class="h-20 w-20 shrink-0 rounded-full object-cover border-2 {{ $accent['border'] }}">
                @else
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full {{ $accent['avatar'] }} font-bold text-2xl border-2 {{ $accent['border'] }}">
                        {{ strtoupper(substr($member->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    @if($canEditName)
                        @if(session('status'))
                            <p class="text-xs text-green-400 mb-1">{{ session('status') }}</p>
                        @endif
                        <form method="POST" action="{{ route('member.profile.update', $member) }}" class="space-y-2">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center gap-2 mb-1">
                                <input type="text" name="name" value="{{ old('name', $member->name) }}"
                                       class="bg-transparent text-2xl font-bold text-white border-b border-white/30 focus:border-white/70 focus:outline-none outline-none w-auto"
                                       aria-label="Display name">
                                <button type="submit" class="text-xs text-gray-400 hover:text-white transition-colors">Save</button>
                            </div>
                            @error('name')
                                <p class="text-xs text-red-400 mb-1">{{ $message }}</p>
                            @enderror
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-gray-500">RSI:</span>
                                <input type="text" name="rsi_handle" value="{{ old('rsi_handle', $member->rsi_handle) }}"
                                       placeholder="Your RSI citizen handle"
                                       class="bg-transparent text-sm text-gray-300 border-b border-white/20 focus:border-white/50 focus:outline-none outline-none w-48"
                                       aria-label="RSI citizen handle">
                            </div>
                            @error('rsi_handle')
                                <p class="text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </form>
                    @else
                        <h1 class="text-2xl font-bold text-white">{{ $member->name }}</h1>
                    @endif
                    <div class="mt-1.5 flex items-center gap-2">
                        @if($member->profile_url)
                            <a href="{{ $member->profile_url }}"
                               target="_blank" rel="noopener noreferrer"
                               aria-label="Discord Profile"
                               title="Discord Profile"
                               class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-400 hover:text-indigo-300 transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                                </svg>
                            </a>
                        @endif
                        @if($member->rsi_handle)
                            <a href="https://robertsspaceindustries.com/en/citizens/{{ $member->rsi_handle }}"
                               target="_blank" rel="noopener noreferrer"
                               aria-label="RSI Profile"
                               title="RSI Profile"
                               class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-blue-400/10 hover:bg-blue-400/20 text-blue-400 hover:text-blue-300 transition-colors">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.82m5.84-2.56a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.82m2.56-5.84a14.98 14.98 0 0 0-6.16 12.12A14.98 14.98 0 0 0 14.37 15.59m.119-8.54 2.113 2.906M15.23 6.32l-2.113-2.906m0 0-2.113 2.906m2.113-2.906L12 3"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                    @if($member->title)
                        <span class="mt-2 inline-block text-xs font-medium {{ $accent['text'] }} bg-white/5 px-3 py-1 rounded-full">
                            {{ $member->title }}
                        </span>
                    @endif
                    @if($member->orgRole)
                        <span class="mt-2 ml-2 inline-block text-xs font-medium {{ $accent['text'] }} bg-white/5 px-3 py-1 rounded-full">
                            {{ $member->orgRole->label }}
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- ─── Training Tracker ─── --}}
        <section>
            <div class="flex items-center gap-3 mb-5">
                <div class="h-8 w-1 rounded bg-blue-400"></div>
                <h2 class="text-xl font-bold tracking-widest uppercase text-blue-400">Training Tracker</h2>
            </div>

            @if($categories->isEmpty())
                <p class="text-sm text-gray-400 italic pl-4">No training data has been recorded for this member yet.</p>
            @else
                <div class="space-y-5">
                    @foreach($categories as $category)
                        <div class="rounded-xl border border-white/10 bg-white/5 p-5">
                            <div class="flex items-center justify-between gap-4 mb-4">
                                @php
                                    $avg = $categoryAverages->get($category->id, 0.0);
                                    if ($avg == 5) {
                                        $badgeLabel = 'Trainer';
                                        $badgeClass = 'inline-block font-medium text-yellow-400 bg-white/5 px-2 py-1';
                                    } elseif ($avg >= 4) {
                                        $badgeLabel = 'Certified';
                                        $badgeClass = 'inline-block font-medium text-green-400 bg-white/5 px-2 py-1';
                                    } else {
                                        $badgeLabel = 'In Training';
                                        $badgeClass = 'inline-block font-medium text-blue-400 bg-white/5 px-2 py-1';
                                    }
                                @endphp
                                <div class="flex items-center gap-2">
                                    @if($category->image)
                                        <img src="{{ Storage::disk('public')->url($category->image) }}"
                                             alt="{{ $category->name }}"
                                             class="h-18 w-18 shrink-0 rounded object-cover">
                                    @endif
                                    <h3 class="text-base font-semibold text-white">{{ $category->name }}</h3>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 ring-1 ring-inset text-xs {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0" aria-label="Overall {{ number_format($avg, 1) }} out of 5 stars">
                                    <span class="text-xs text-gray-400 mr-0.5">Overall:</span>
                                    @for($i = 1; $i <= 5; $i++)
                                        @php $fill = min(1, max(0, $avg - ($i - 1))); @endphp
                                        @if($fill >= 1)
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @elseif($fill >= 0.5)
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" aria-hidden="true">
                                                <defs>
                                                    <linearGradient id="overall-half-{{ $category->id }}-{{ $i }}">
                                                        <stop offset="50%" stop-color="rgb(250 204 21)"/>
                                                        <stop offset="50%" stop-color="rgb(55 65 81)"/>
                                                    </linearGradient>
                                                </defs>
                                                <path fill="url(#overall-half-{{ $category->id }}-{{ $i }})"
                                                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                    <span class="ml-1 text-xs text-gray-500">{{ number_format($avg, 1) }}</span>
                                </div>
                            </div>

                            @if($category->subtopics->isEmpty())
                                <p class="text-xs text-gray-500 italic">No subtopics configured.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($category->subtopics as $subtopic)
                                        @php
                                            $rating = (float) ($ratings[$subtopic->id] ?? 0);
                                        @endphp
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="min-w-0 flex items-center gap-1.5">
                                                <span class="text-sm text-gray-300 truncate">{{ $subtopic->name }}</span>
                                                @if($subtopic->description)
                                                    <span class="relative group shrink-0">
                                                        <svg class="h-3.5 w-3.5 text-gray-500 cursor-help" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="pointer-events-none absolute left-1/2 bottom-full mb-2 -translate-x-1/2 w-56 rounded bg-gray-800 border border-white/10 px-3 py-2 text-xs text-gray-200 opacity-0 group-hover:opacity-100 transition-opacity z-10 shadow-lg">
                                                            {{ $subtopic->description }}
                                                        </span>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1 shrink-0" aria-label="{{ $rating }} out of 5 stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @php
                                                        $fill = min(1, max(0, $rating - ($i - 1)));
                                                    @endphp
                                                    @if($fill >= 1)
                                                        {{-- Full star --}}
                                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @elseif($fill >= 0.5)
                                                        {{-- Half star --}}
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" aria-hidden="true">
                                                            <defs>
                                                                <linearGradient id="half-{{ $category->id }}-{{ $subtopic->id }}-{{ $i }}">
                                                                    <stop offset="50%" stop-color="rgb(250 204 21)"/>
                                                                    <stop offset="50%" stop-color="rgb(55 65 81)"/>
                                                                </linearGradient>
                                                            </defs>
                                                            <path fill="url(#half-{{ $category->id }}-{{ $subtopic->id }}-{{ $i }})"
                                                                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @else
                                                        {{-- Empty star --}}
                                                        <svg class="h-5 w-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endif
                                                @endfor
                                                    <span class="ml-1 text-xs text-gray-500">{{ number_format($rating, 1) }}</span>
                                            </div>
                                        </div>
                                        @php $ratingNote = $notesData->get($subtopic->id); @endphp
                                        @if($ratingNote && $ratingNote->note)
                                            <div class="mt-1.5 border-l-2 border-blue-800/50 pl-3">
                                                <p class="text-xs text-gray-400 italic">{{ $ratingNote->note }}</p>
                                                @if($ratingNote->noteAuthor)
                                                    <p class="text-xs text-gray-600 mt-0.5">— {{ $ratingNote->noteAuthor->name }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

    </main>

</body>
</html>
