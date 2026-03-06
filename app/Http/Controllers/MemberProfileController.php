<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MeritRedemption;
use App\Models\OrgRole;
use App\Models\Reward;
use App\Models\RewardCategory;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamRole;
use App\Models\TrainingCategory;
use App\Services\DiscordService;
use Illuminate\Http\Request;

class MemberProfileController extends Controller
{
    /**
     * Sync a member's data from the Discord guild: display name, avatar, org role,
     * and team memberships. Admin-only. The member must have a discord_id linked.
     *
     * Display name priority: server nickname → global display name → username.
     */
    public function syncFromDiscord(Member $member)
    {
        abort_unless(auth()->user()->is_admin, 403);
        abort_unless($member->discord_id, 422, 'This member has no Discord ID linked.');

        $discord = app(DiscordService::class);
        $dm = $discord->getGuildMember($member->discord_id);

        if ($dm === null) {
            return redirect()->route('member.profile', $member)
                ->with('status', 'Member not found in the Discord guild.');
        }

        $displayName = DiscordService::resolveDisplayName($dm);

        // Resolve highest-priority org role from the member's Discord roles
        $roleMap = [];
        OrgRole::whereNotNull('discord_role_ids')
            ->orderBy('sort_order')
            ->get(['id', 'discord_role_ids', 'sort_order'])
            ->each(function ($orgRole) use (&$roleMap) {
                foreach ($orgRole->discord_role_ids ?? [] as $discordRoleId) {
                    if ($discordRoleId === '') {
                        continue;
                    }
                    if (! isset($roleMap[$discordRoleId])) {
                        $roleMap[$discordRoleId] = $orgRole;
                    }
                }
            });

        $orgRoleId = null;
        $bestSortOrder = PHP_INT_MAX;
        foreach ($dm['role_ids'] as $discordRoleId) {
            if (isset($roleMap[$discordRoleId])) {
                $candidate = $roleMap[$discordRoleId];
                if ($candidate->sort_order < $bestSortOrder) {
                    $orgRoleId = $candidate->id;
                    $bestSortOrder = $candidate->sort_order;
                }
            }
        }

        $updateData = [
            'name'       => $displayName,
            'avatar_url' => $dm['avatar_url'],
        ];
        if ($orgRoleId !== null) {
            $updateData['org_role_id'] = $orgRoleId;
        }
        $member->update($updateData);

        // Sync team memberships
        $teamMap = Team::whereNotNull('discord_role_id')
            ->where('discord_role_id', '!=', '')
            ->get(['id', 'discord_role_id', 'unit_id'])
            ->keyBy('discord_role_id');

        foreach ($dm['role_ids'] as $discordRoleId) {
            if (isset($teamMap[$discordRoleId])) {
                TeamMember::firstOrCreate([
                    'team_id'   => $teamMap[$discordRoleId]->id,
                    'member_id' => $member->id,
                ]);
            }
        }

        // Sync team roles
        $teamRoleMap = TeamRole::whereNotNull('discord_role_id')
            ->where('discord_role_id', '!=', '')
            ->get(['id', 'discord_role_id', 'unit_id', 'sort_order'])
            ->keyBy('discord_role_id');

        $teamRoleByUnit = [];
        foreach ($dm['role_ids'] as $discordRoleId) {
            if (isset($teamRoleMap[$discordRoleId])) {
                $teamRole = $teamRoleMap[$discordRoleId];
                $unitId   = $teamRole->unit_id;
                if (! isset($teamRoleByUnit[$unitId]) || $teamRole->sort_order < $teamRoleByUnit[$unitId]->sort_order) {
                    $teamRoleByUnit[$unitId] = $teamRole;
                }
            }
        }
        foreach ($teamRoleByUnit as $unitId => $teamRole) {
            TeamMember::whereHas('team', fn ($q) => $q->where('unit_id', $unitId))
                ->where('member_id', $member->id)
                ->update(['team_role_id' => $teamRole->id]);
        }

        return redirect()->route('member.profile', $member)
            ->with('status', 'Member synced from Discord successfully.');
    }

    public function update(Request $request, Member $member)
    {
        $user = auth()->user();

        abort_unless(
            $member->discord_id && $user->discord_id === $member->discord_id,
            403
        );

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'rsi_handle' => ['nullable', 'string', 'max:255'],
        ]);

        $member->update([
            'name'       => $validated['name'],
            'rsi_handle' => $validated['rsi_handle'] ?? null,
        ]);

        return redirect()->route('member.profile', $member)
            ->with('status', 'Profile updated successfully.');
    }

    public function show(Member $member)
    {
        $categories = TrainingCategory::with([
            'subtopics' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])
            ->whereHas('subtopics.ratings', function ($query) use ($member) {
                $query->where('member_id', $member->id);
            })
            ->orderBy('sort_order')
            ->get();

        $ratings = $member->trainingRatings()
            ->pluck('rating', 'training_subtopic_id');

        // auth middleware on this route guarantees auth()->user() is non-null
        $canViewNotes = auth()->user()->is_admin
            || ($member->discord_id && auth()->user()->discord_id === $member->discord_id);

        $notesData = $canViewNotes
            ? $member->trainingRatings()
                ->with('noteAuthor')
                ->whereNotNull('note')
                ->get()
                ->keyBy('training_subtopic_id')
            : collect();

        $categoryAverages = $categories->mapWithKeys(function ($category) use ($ratings) {
            $subtopicIds = $category->subtopics->pluck('id');
            $categoryRatings = $ratings->only($subtopicIds);

            return [$category->id => $categoryRatings->isNotEmpty() ? (float) $categoryRatings->avg() : 0.0];
        });

        $canEditName = $member->discord_id && auth()->user()->discord_id === $member->discord_id;

        $isAdmin = auth()->user()->is_admin;

        $teamMemberships = $member->teamMembers()
            ->with(['team', 'teamRole'])
            ->orderBy('sort_order')
            ->get();

        $canRedeem = $isAdmin
            || ($member->discord_id && auth()->user()->discord_id === $member->discord_id);

        $rewardCategories = RewardCategory::with([
            'rewards' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])
            ->orderBy('sort_order')
            ->get();

        return view('member-profile', compact(
            'member',
            'categories',
            'ratings',
            'categoryAverages',
            'notesData',
            'canEditName',
            'isAdmin',
            'teamMemberships',
            'canRedeem',
            'rewardCategories',
        ));
    }

    public function redeem(Member $member, Reward $reward)
    {
        $user = auth()->user();

        $isOwnProfile = $member->discord_id && $user->discord_id === $member->discord_id;
        abort_unless($user->is_admin || $isOwnProfile, 403);

        if ($member->merits < $reward->merit_cost) {
            return redirect()->route('member.profile', $member)
                ->with('status', 'Not enough merits to redeem this reward.')
                ->with('status_type', 'error');
        }

        MeritRedemption::create([
            'member_id'           => $member->id,
            'reward_id'           => $reward->id,
            'merit_cost'          => $reward->merit_cost,
            'redeemed_by_user_id' => $user->id,
        ]);

        $member->decrement('merits', $reward->merit_cost);

        return redirect()->route('member.profile', $member)
            ->with('status', "Successfully redeemed \"{$reward->name}\" for {$reward->merit_cost} merits.")
            ->with('status_type', 'success');
    }
}
