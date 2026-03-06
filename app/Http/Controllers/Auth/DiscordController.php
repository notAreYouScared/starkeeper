<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Services\DiscordService;
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
        $user = User::where('email', $discordUser->getEmail())->first();

        $user?->update([
            'avatar' => $discordUser->getAvatar(),
        ]);

        $member = Member::where('discord_id', $discordUser->getId())->first();

        if ($member) {
            $member?->update([
                'avatar_url' => $discordUser->getAvatar(),
            ]);

            Auth::login($user, remember: true);
        }
        return redirect()->intended(route('home'));
    }
}
