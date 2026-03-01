<?php

use App\Http\Controllers\Auth\DiscordController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\HierarchyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $page = \App\Models\ContentPage::where('slug', 'home')->first();
    return view('home', compact('page'));
})->name('home');

Route::get('/hierarchy', [HierarchyController::class, 'index'])
    ->name('hierarchy');

Route::get('/history', function () {
    $page = \App\Models\ContentPage::where('slug', 'history')->first();
    return view('history', compact('page'));
})->name('history');

Route::get('/manifesto', function () {
    $page = \App\Models\ContentPage::where('slug', 'manifesto')->first();
    return view('manifesto', compact('page'));
})->name('manifesto');

Route::get('/charter', function () {
    $page = \App\Models\ContentPage::where('slug', 'charter')->first();
    return view('charter', compact('page'));
})->name('charter');

Route::middleware('auth')->group(function () {
    Route::get('/members/{member}', [MemberProfileController::class, 'show'])
        ->name('member.profile');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/discord/redirect', [DiscordController::class, 'redirect'])
        ->name('auth.discord.redirect');
    Route::get('/auth/discord/callback', [DiscordController::class, 'callback'])
        ->name('auth.discord.callback');
});
