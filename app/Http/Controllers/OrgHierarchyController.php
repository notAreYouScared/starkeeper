<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Unit;

class OrgHierarchyController extends Controller
{
    public function index()
    {
        $leaders = Member::where('org_role', 'leadership')
            ->orderBy('name')
            ->get();

        $units = Unit::with(['teams.teamMembers.member'])
            ->orderBy('name')
            ->get();

        return view('org-hierarchy', compact('leaders', 'units'));
    }
}
