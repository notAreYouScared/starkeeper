<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-950 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manifesto – Starkeeper</title>
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

    <x-footer />

</body>
</html>
