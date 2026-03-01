<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MemberTrainingRating;
use App\Models\OrgRole;
use App\Models\TrainingCategory;
use App\Models\TrainingSubtopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingTrackerTest extends TestCase
{
    use RefreshDatabase;

    private function createOrgRole(): OrgRole
    {
        return OrgRole::create(['name' => 'member', 'label' => 'Member', 'sort_order' => 1]);
    }

    private function createMember(): Member
    {
        $role = $this->createOrgRole();

        return Member::create([
            'name'       => 'Test Pilot',
            'handle'     => 'testpilot',
            'org_role_id' => $role->id,
            'sort_order' => 0,
        ]);
    }

    public function test_member_profile_requires_authentication(): void
    {
        $member = $this->createMember();

        $response = $this->get(route('member.profile', $member));

        $response->assertRedirect();
        $this->assertStringContainsString('login', $response->headers->get('Location'));
    }

    public function test_authenticated_user_can_view_member_profile(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertViewIs('member-profile');
        $response->assertViewHas('member', $member);
    }

    public function test_member_profile_shows_training_categories_and_subtopics(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Dogfighting',
            'sort_order'           => 1,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 3.0,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Combat');
        $response->assertSee('Dogfighting');
    }

    public function test_member_profile_shows_star_rating_for_subtopic(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Dogfighting',
            'sort_order'           => 1,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 3.5,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('3.5');
    }

    public function test_training_category_model_has_subtopics(): void
    {
        $category = TrainingCategory::create(['name' => 'Logistics', 'sort_order' => 1]);
        TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Cargo',
            'sort_order'           => 1,
        ]);
        TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Hauling',
            'sort_order'           => 2,
        ]);

        $this->assertCount(2, $category->subtopics);
    }

    public function test_member_has_training_ratings_relationship(): void
    {
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Mining', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Laser Mining',
            'sort_order'           => 1,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 4.5,
        ]);

        $this->assertCount(1, $member->trainingRatings);
        $this->assertEquals(4.5, $member->trainingRatings->first()->rating);
    }

    public function test_rating_is_unique_per_member_per_subtopic(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Dogfighting',
            'sort_order'           => 1,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 3.0,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 4.0,
        ]);
    }

    public function test_member_profile_shows_overall_category_rating(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);

        $subtopic1 = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Dogfighting',
            'sort_order'           => 1,
        ]);
        $subtopic2 = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Formation Flying',
            'sort_order'           => 2,
        ]);

        MemberTrainingRating::create(['member_id' => $member->id, 'training_subtopic_id' => $subtopic1->id, 'rating' => 4.0]);
        MemberTrainingRating::create(['member_id' => $member->id, 'training_subtopic_id' => $subtopic2->id, 'rating' => 3.0]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        // Average of 4.0 + 3.0 = 3.5
        $response->assertSee('Overall:');
        $response->assertSee('3.5');
    }

    public function test_member_profile_does_not_show_categories_without_member_ratings(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Logistics', 'sort_order' => 1]);
        TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Cargo',
            'sort_order'           => 1,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertDontSee('Logistics');
        $response->assertDontSee('Overall:');
        $response->assertSee('No training data has been recorded for this member yet.');
    }


    public function test_member_profile_shows_in_training_badge_when_avg_below_4(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create(['training_category_id' => $category->id, 'name' => 'Dogfighting', 'sort_order' => 1]);

        MemberTrainingRating::create(['member_id' => $member->id, 'training_subtopic_id' => $subtopic->id, 'rating' => 3.0]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('In Training');
        $response->assertDontSee('Certified');
        $response->assertDontSee('Trainer');
    }

    public function test_member_profile_shows_certified_badge_when_avg_gte_4(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create(['training_category_id' => $category->id, 'name' => 'Dogfighting', 'sort_order' => 1]);

        MemberTrainingRating::create(['member_id' => $member->id, 'training_subtopic_id' => $subtopic->id, 'rating' => 4.0]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Certified');
        $response->assertDontSee('Trainer');
        $response->assertDontSee('In Training');
    }

    public function test_member_profile_shows_trainer_badge_when_avg_is_5(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create(['training_category_id' => $category->id, 'name' => 'Dogfighting', 'sort_order' => 1]);

        MemberTrainingRating::create(['member_id' => $member->id, 'training_subtopic_id' => $subtopic->id, 'rating' => 5.0]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Trainer');
        $response->assertDontSee('Certified');
        $response->assertDontSee('In Training');
    }


    public function test_member_profile_shows_empty_state_when_no_categories(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('No training data has been recorded for this member yet.');
    }

    public function test_subtopic_description_is_stored_and_shown_as_tooltip(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Ship Mining', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Mining head operation',
            'description'          => 'Knows how to properly charge and fire the mining head.',
            'sort_order'           => 1,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 3.0,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Mining head operation');
        $response->assertSee('Knows how to properly charge and fire the mining head.');
    }

    public function test_subtopic_description_is_nullable(): void
    {
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Dogfighting',
            'sort_order'           => 1,
        ]);

        $this->assertNull($subtopic->description);
    }

    public function test_member_profile_shows_note_under_subtopic(): void
    {
        $admin    = User::factory()->create(['is_admin' => true]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Navigation', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Jump point navigation',
            'sort_order'           => 1,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 3.0,
            'note'                 => 'Needs more practice on large jump points.',
            'note_author_id'       => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Needs more practice on large jump points.');
        $response->assertSee($admin->name);
    }

    public function test_member_profile_does_not_show_note_when_empty(): void
    {
        $user     = User::factory()->create(['is_admin' => false]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Navigation', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Jump point navigation',
            'sort_order'           => 1,
        ]);

        MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 4.0,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Jump point navigation');
        // No note div rendered when note is null
        $response->assertDontSee('border-blue-800/50');
    }

    public function test_training_rating_note_is_stored_with_author(): void
    {
        $admin    = User::factory()->create(['is_admin' => true]);
        $member   = $this->createMember();
        $category = TrainingCategory::create(['name' => 'Combat', 'sort_order' => 1]);
        $subtopic = TrainingSubtopic::create([
            'training_category_id' => $category->id,
            'name'                 => 'Dogfighting',
            'sort_order'           => 1,
        ]);

        $rating = MemberTrainingRating::create([
            'member_id'            => $member->id,
            'training_subtopic_id' => $subtopic->id,
            'rating'               => 2.5,
            'note'                 => 'Improving steadily.',
            'note_author_id'       => $admin->id,
        ]);

        $this->assertEquals('Improving steadily.', $rating->note);
        $this->assertEquals($admin->id, $rating->note_author_id);
        $this->assertEquals($admin->name, $rating->noteAuthor->name);
    }

    public function test_training_category_stores_image(): void
    {
        $category = TrainingCategory::create([
            'name'       => 'Ship Systems',
            'sort_order' => 1,
            'image'      => 'training-category-images/test.png',
        ]);

        $this->assertEquals('training-category-images/test.png', $category->image);
    }
}
