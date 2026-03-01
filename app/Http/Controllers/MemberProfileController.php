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
            ->whereHas('subtopics.ratings', function ($query) use ($member) {
                $query->where('member_id', $member->id);
            })
            ->orderBy('sort_order')
            ->get();

        $ratings = $member->trainingRatings()
            ->pluck('rating', 'training_subtopic_id');

        $notesData = $member->trainingRatings()
            ->with('noteAuthor')
            ->whereNotNull('note')
            ->get()
            ->keyBy('training_subtopic_id');

        $categoryAverages = $categories->mapWithKeys(function ($category) use ($ratings) {
            $subtopicIds = $category->subtopics->pluck('id');
            $categoryRatings = $ratings->only($subtopicIds);

            return [$category->id => $categoryRatings->isNotEmpty() ? (float) $categoryRatings->avg() : 0.0];
        });

        return view('member-profile', compact('member', 'categories', 'ratings', 'categoryAverages', 'notesData'));
    }
}
