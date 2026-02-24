<?php

use App\Http\Controllers\Auth\DiscordController;
use App\Http\Controllers\OrgHierarchyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/org-hierarchy', [OrgHierarchyController::class, 'index'])
    ->name('org-hierarchy');

Route::middleware('guest')->group(function () {
    Route::get('/auth/discord/redirect', [DiscordController::class, 'redirect'])
        ->name('auth.discord.redirect');
    Route::get('/auth/discord/callback', [DiscordController::class, 'callback'])
        ->name('auth.discord.callback');
});
