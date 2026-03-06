<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\OrgRole;
use App\Models\Team;
use App\Models\Unit;
use App\Services\DiscordService;

class HierarchyController extends Controller
{
    public function index()
    {
        $orgRoles = OrgRole::orderBy('sort_order')->get();

        $membersByRole = [];
        foreach ($orgRoles as $role) {
            $members = Member::where('org_role_id', $role->id)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            if ($members->isNotEmpty()) {
                $membersByRole[] = [
                    'role' => $role,
                    'members' => $members,
                ];
            }
        }

        $units = Unit::with(['teams.teamMembers' => function ($query) {
            $query->orderBy('sort_order')->orderBy('id');
        }, 'teams.teamMembers.member', 'teams.teamMembers.teamRole'])
            ->orderBy('name')
            ->get();

        return view('hierarchy', compact('membersByRole', 'units'));
    }

    public function joinRequest(Team $team)
    {
        abort_unless($team->show_join_request, 403);

        $user = auth()->user();

        abort_unless($user->discord_id, 422, 'Your account has no Discord ID linked.');

        $owner = $team->owner;

        if (! $owner || ! $owner->discord_id) {
            return redirect()->route('hierarchy')
                ->with('join_request_error', 'Unable to send request: this team has no owner with a linked Discord account.');
        }

        $message = "<@{$user->discord_id}> has interest in joining {$team->name}";

        $userMessage = trim(request('message', ''));
        if ($userMessage !== '') {
            $message .= "\n\n{$userMessage}";
        }

        app(DiscordService::class)->sendDirectMessage($owner->discord_id, $message);

        return redirect()->route('hierarchy')
            ->with('join_request_success', "Your request to join {$team->name} has been sent.");
    }
}
