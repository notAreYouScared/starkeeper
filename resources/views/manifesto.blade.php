<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manifesto – StarKeeper</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full bg-gray-950 text-gray-100 antialiased">

    <x-nav active-page="manifesto" breadcrumb="Manifesto" />
    <main class="mx-auto max-w-3xl px-4 py-12">
        <div class="flex items-center gap-3 mb-8">
            <div class="h-10 w-1 rounded bg-green-400"></div>
            <h1 class="text-3xl font-black tracking-widest uppercase text-green-400">Manifesto</h1>
        </div>
        <div class="prose prose-invert max-w-none text-gray-300 leading-relaxed">
            @if($page)
            {!! \Illuminate\Support\Str::markdown($page->content) !!}
            @endif
        </div>
    </main>

    <footer class="border-t border-white/10 mt-12">
        <div class="mx-auto max-w-5xl px-4 py-6 flex flex-wrap items-center justify-between gap-4 text-xs text-gray-500">
            <span>&copy; {{ date('Y') }} Starkeeper. All rights reserved.</span>
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
