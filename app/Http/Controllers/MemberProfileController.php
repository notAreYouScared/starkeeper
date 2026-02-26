<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\TrainingCategory;

class MemberProfileController extends Controller
{
    public function show(Member $member)
    {
        $categories = TrainingCategory::with([
            'subtopics' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])
            ->orderBy('sort_order')
            ->get();

        $ratings = $member->trainingRatings()
            ->pluck('rating', 'training_subtopic_id');

        return view('member-profile', compact('member', 'categories', 'ratings'));
    }
}
