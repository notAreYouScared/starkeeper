<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $member->name }} – StarKeeper</title>
    @vite(['resources/css/app.css'])
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
                <span class="text-sm text-gray-300">{{ $member->name }}</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('home') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Home</a>
                <a href="{{ route('org-hierarchy') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Hierarchy</a>
                <a href="{{ route('history') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">History</a>
                <a href="{{ route('manifesto') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Manifesto</a>
                <a href="{{ route('charter') }}"
                   class="text-gray-300 hover:text-blue-400 transition-colors">Charter</a>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                       class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Admin Panel →</a>
                @endauth
            </nav>
        </div>
    </header>

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
                    <h1 class="text-2xl font-bold text-white">{{ $member->name }}</h1>
                    @if($member->handle)
                        <p class="text-sm text-gray-400 mt-0.5">{{ '@'.$member->handle }}</p>
                    @endif
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
