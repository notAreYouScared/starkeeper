<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\OrgRole;
use App\Models\Unit;

class OrgHierarchyController extends Controller
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
        }, 'teams.teamMembers.member'])
            ->orderBy('name')
            ->get();

        return view('org-hierarchy', compact('membersByRole', 'units'));
    }
}
