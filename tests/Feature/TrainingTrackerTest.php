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

    public function test_member_profile_shows_empty_state_when_no_categories(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('No training categories have been configured yet');
    }
}
