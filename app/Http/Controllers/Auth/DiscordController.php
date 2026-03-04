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

        if ($user) {
            $user->update([
                'avatar' => $discordUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'discord_id' => $discordUser->getId(),
                'name'       => $discordUser->getName() ?? $discordUser->getNickname(),
                'email'      => $discordUser->getEmail(),
                'avatar'     => $discordUser->getAvatar(),
            ]);
        }

        $member = Member::where('discord_id', $discordUser->getId())->first();

        if ($member) {
            $member->update([
                'name'       => $discordUser->getName() ?? $discordUser->getNickname(),
                'avatar_url' => $discordUser->getAvatar(),
            ]);
        } else {
            Member::create([
                'discord_id'  => $discordUser->getId(),
                'name'        => $discordUser->getName() ?? $discordUser->getNickname(),
                'handle'      => $discordUser->getNickname(),
                'avatar_url'  => $discordUser->getAvatar(),
                'profile_url' => DiscordService::profileUrl($discordUser->getId()),
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}
