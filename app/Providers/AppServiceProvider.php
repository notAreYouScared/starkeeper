<?php

namespace App\Providers;

use App\Models\Member;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app['events']->listen(
            SocialiteWasCalled::class,
            \SocialiteProviders\Discord\DiscordExtendSocialite::class
        );

        View::composer('components.nav', function ($view) {
            $user = auth()->user();
            $myMember = ($user && $user->discord_id)
                ? Member::where('discord_id', $user->discord_id)->first()
                : null;
            $view->with('myMember', $myMember);
        });
    }
}
