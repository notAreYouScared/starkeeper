<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\TrainingCategory;
use Illuminate\Http\Request;

class MemberProfileController extends Controller
{
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

        return view('member-profile', compact('member', 'categories', 'ratings', 'categoryAverages', 'notesData', 'canEditName', 'isAdmin'));
    }
}
