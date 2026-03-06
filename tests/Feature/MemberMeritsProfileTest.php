<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MeritRedemption;
use App\Models\OrgRole;
use App\Models\Reward;
use App\Models\RewardCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberMeritsProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createOrgRole(): OrgRole
    {
        return OrgRole::create(['name' => 'member', 'label' => 'Member', 'sort_order' => 1]);
    }

    private function createMember(array $attrs = []): Member
    {
        return Member::create(array_merge([
            'name'        => 'Test Pilot',
            'handle'      => 'testpilot',
            'org_role_id' => $this->createOrgRole()->id,
            'sort_order'  => 0,
            'merits'      => 0,
        ], $attrs));
    }

    private function createUserLinkedToMember(Member $member, bool $isAdmin = false): User
    {
        $discord_id = 'discord_' . $member->id;
        $member->update(['discord_id' => $discord_id]);

        return User::factory()->create([
            'is_admin'   => $isAdmin,
            'discord_id' => $discord_id,
        ]);
    }

    private function createReward(int $meritCost = 50): Reward
    {
        $category = RewardCategory::create(['name' => 'Test Category', 'sort_order' => 1]);

        return Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Test Reward',
            'merit_cost'         => $meritCost,
            'sort_order'         => 1,
        ]);
    }

    // -----------------------------------------------------------------------
    // Member profile merit balance display
    // -----------------------------------------------------------------------

    public function test_member_profile_shows_merit_balance(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember(['merits' => 150]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('150');
        $response->assertSee('Merits');
    }

    public function test_member_profile_shows_zero_merits_by_default(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember(['merits' => 0]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('0 Merits');
    }

    // -----------------------------------------------------------------------
    // Rewards store display
    // -----------------------------------------------------------------------

    public function test_member_profile_shows_rewards_store_section(): void
    {
        $member = $this->createMember(['merits' => 0]);
        $user   = $this->createUserLinkedToMember($member);
        $this->createReward(50);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Rewards Store');
        $response->assertSee('Test Reward');
        $response->assertSee('Test Category');
    }

    public function test_member_profile_shows_empty_rewards_message_when_no_rewards(): void
    {
        $member = $this->createMember();
        $user   = $this->createUserLinkedToMember($member);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('No rewards are available yet');
    }

    public function test_member_profile_shows_merit_cost_on_rewards(): void
    {
        $member = $this->createMember();
        $user   = $this->createUserLinkedToMember($member);
        $this->createReward(75);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('75 merits');
    }

    // -----------------------------------------------------------------------
    // Redeem button logic (own profile)
    // -----------------------------------------------------------------------

    public function test_own_profile_can_see_redeem_button_when_enough_merits(): void
    {
        $member = $this->createMember(['merits' => 100]);
        $user   = $this->createUserLinkedToMember($member);
        $this->createReward(50);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Redeem');
        $response->assertDontSee('Missing Merits');
    }

    public function test_own_profile_redeem_button_disabled_when_not_enough_merits(): void
    {
        $member = $this->createMember(['merits' => 10]);
        $user   = $this->createUserLinkedToMember($member);
        $this->createReward(100);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Missing Merits');
    }

    public function test_other_member_cannot_see_redeem_button(): void
    {
        $member    = $this->createMember(['merits' => 100]);
        $otherUser = User::factory()->create(['is_admin' => false, 'discord_id' => 'different_discord_id']);
        $this->createReward(50);

        $response = $this->actingAs($otherUser)->get(route('member.profile', $member));

        $response->assertStatus(200);
        // The entire Rewards Store tab is hidden for unrelated users
        $response->assertDontSee('Rewards Store');
        $response->assertDontSee('Redeem');
    }

    public function test_unrelated_user_cannot_see_rewards_store_tab(): void
    {
        $member    = $this->createMember(['merits' => 100]);
        $otherUser = User::factory()->create(['is_admin' => false, 'discord_id' => 'different_discord_id']);
        $this->createReward(50);

        $response = $this->actingAs($otherUser)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertDontSee('Rewards Store');
        // The tab button and panel elements are not rendered
        $response->assertDontSee('id="tab-rewards"', false);
        $response->assertDontSee('id="panel-rewards"', false);
    }

    public function test_admin_can_see_redeem_button(): void
    {
        $member = $this->createMember(['merits' => 100]);
        $admin  = User::factory()->create(['is_admin' => true]);
        $this->createReward(50);

        $response = $this->actingAs($admin)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Redeem');
    }

    // -----------------------------------------------------------------------
    // Redeem POST route
    // -----------------------------------------------------------------------

    public function test_member_can_redeem_reward_with_sufficient_merits(): void
    {
        $member = $this->createMember(['merits' => 100]);
        $user   = $this->createUserLinkedToMember($member);
        $reward = $this->createReward(50);

        $response = $this->actingAs($user)->post(route('member.redeem', [$member, $reward]));

        $response->assertRedirect(route('member.profile', $member));
        $member->refresh();
        $this->assertEquals(50, $member->merits);
        $this->assertDatabaseHas('merit_redemptions', [
            'member_id'           => $member->id,
            'reward_id'           => $reward->id,
            'merit_cost'          => 50,
            'redeemed_by_user_id' => $user->id,
        ]);
    }

    public function test_member_cannot_redeem_reward_without_sufficient_merits(): void
    {
        $member = $this->createMember(['merits' => 10]);
        $user   = $this->createUserLinkedToMember($member);
        $reward = $this->createReward(100);

        $response = $this->actingAs($user)->post(route('member.redeem', [$member, $reward]));

        $response->assertRedirect(route('member.profile', $member));
        $response->assertSessionHas('status_type', 'error');
        $member->refresh();
        $this->assertEquals(10, $member->merits); // unchanged
        $this->assertDatabaseMissing('merit_redemptions', [
            'member_id' => $member->id,
            'reward_id' => $reward->id,
        ]);
    }

    public function test_admin_can_redeem_reward_for_member(): void
    {
        $member = $this->createMember(['merits' => 200]);
        $admin  = User::factory()->create(['is_admin' => true]);
        $reward = $this->createReward(100);

        $response = $this->actingAs($admin)->post(route('member.redeem', [$member, $reward]));

        $response->assertRedirect(route('member.profile', $member));
        $member->refresh();
        $this->assertEquals(100, $member->merits);
        $this->assertDatabaseHas('merit_redemptions', [
            'member_id'           => $member->id,
            'reward_id'           => $reward->id,
            'merit_cost'          => 100,
            'redeemed_by_user_id' => $admin->id,
        ]);
    }

    public function test_unrelated_user_cannot_redeem_reward_for_another_member(): void
    {
        $member    = $this->createMember(['merits' => 200]);
        $otherUser = User::factory()->create(['is_admin' => false, 'discord_id' => 'different_discord_id']);
        $reward    = $this->createReward(50);

        $response = $this->actingAs($otherUser)->post(route('member.redeem', [$member, $reward]));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('merit_redemptions', [
            'member_id' => $member->id,
            'reward_id' => $reward->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_redeem(): void
    {
        $member = $this->createMember(['merits' => 200]);
        $reward = $this->createReward(50);

        $response = $this->post(route('member.redeem', [$member, $reward]));

        $response->assertRedirect();
        $this->assertStringContainsString('login', $response->headers->get('Location'));
    }

    public function test_successful_redemption_shows_flash_message(): void
    {
        $member = $this->createMember(['merits' => 100]);
        $user   = $this->createUserLinkedToMember($member);
        $reward = $this->createReward(50);

        $response = $this->actingAs($user)->post(route('member.redeem', [$member, $reward]));

        $response->assertRedirect(route('member.profile', $member));
        $response->assertSessionHas('status_type', 'success');
        $response->assertSessionHas('status');
    }

    // -----------------------------------------------------------------------
    // Reward image
    // -----------------------------------------------------------------------

    public function test_reward_image_is_shown_when_set(): void
    {
        $member = $this->createMember(['merits' => 50]);
        $user   = $this->createUserLinkedToMember($member);

        $category = RewardCategory::create(['name' => 'Test Category', 'sort_order' => 1]);
        Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Fancy Reward',
            'merit_cost'         => 50,
            'sort_order'         => 1,
            'image'              => 'reward-images/fancy.png',
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('reward-images/fancy.png', false);
        $response->assertSee('Fancy Reward');
    }

    public function test_reward_image_is_not_shown_when_not_set(): void
    {
        $member = $this->createMember();
        $user   = $this->createUserLinkedToMember($member);

        $category = RewardCategory::create(['name' => 'Test Category', 'sort_order' => 1]);
        Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Plain Reward',
            'merit_cost'         => 50,
            'sort_order'         => 1,
            'image'              => null,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Plain Reward');
        $response->assertDontSee('reward-images/', false);
    }

    // -----------------------------------------------------------------------
    // Tab structure
    // -----------------------------------------------------------------------

    public function test_member_profile_shows_tab_bar_with_training_and_rewards_tabs(): void
    {
        $member = $this->createMember();
        $user   = $this->createUserLinkedToMember($member);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('role="tab"', false);
        $response->assertSee('Training Tracker');
        $response->assertSee('Rewards Store');
        $response->assertSee('role="tabpanel"', false);
    }

    public function test_training_tab_is_active_by_default(): void
    {
        $member = $this->createMember();
        $user   = $this->createUserLinkedToMember($member);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        // Training tab has aria-selected="true"
        $response->assertSee('aria-selected="true"', false);
        // Training panel is present and visible
        $response->assertSee('id="panel-training"', false);
        // Rewards panel is present but starts hidden
        $response->assertSee('id="panel-rewards"', false);
        $response->assertSee('class="hidden"', false);
    }
}
