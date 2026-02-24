<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class DiscordController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('discord')->redirect();
    }

    public function callback()
    {
        $discordUser = Socialite::driver('discord')->user();

        $user = User::updateOrCreate(
            ['discord_id' => $discordUser->getId()],
            [
                'name'   => $discordUser->getNickname(),
                'email'  => $discordUser->getEmail(),
                'avatar' => $discordUser->getAvatar(),
            ]
        );

        Member::updateOrCreate(
            ['discord_id' => $discordUser->getId()],
            [
                'name'        => $discordUser->getNickname(),
                'handle'      => $discordUser->getNickname(),
                'avatar_url'  => $discordUser->getAvatar(),
                'profile_url' => 'discord://-/users/' . $discordUser->getId(),
            ]
        );

        Auth::login($user, remember: true);

        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}
