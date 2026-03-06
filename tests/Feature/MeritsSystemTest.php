<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MeritAward;
use App\Models\MeritRedemption;
use App\Models\OrgRole;
use App\Models\Reward;
use App\Models\RewardCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeritsSystemTest extends TestCase
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

    private function createAdmin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    // -----------------------------------------------------------------------
    // MeritAward model tests
    // -----------------------------------------------------------------------

    public function test_merit_award_can_be_created(): void
    {
        $admin  = $this->createAdmin();
        $member = $this->createMember();

        $award = MeritAward::create([
            'member_id'          => $member->id,
            'awarded_by_user_id' => $admin->id,
            'amount'             => 50,
            'reason'             => 'Outstanding event performance',
        ]);

        $this->assertDatabaseHas('merit_awards', [
            'member_id'          => $member->id,
            'awarded_by_user_id' => $admin->id,
            'amount'             => 50,
            'reason'             => 'Outstanding event performance',
        ]);
        $this->assertEquals(50, $award->amount);
    }

    public function test_merit_award_belongs_to_member(): void
    {
        $admin  = $this->createAdmin();
        $member = $this->createMember();

        $award = MeritAward::create([
            'member_id'          => $member->id,
            'awarded_by_user_id' => $admin->id,
            'amount'             => 10,
            'reason'             => 'Test reason',
        ]);

        $this->assertEquals($member->id, $award->member->id);
    }

    public function test_merit_award_belongs_to_awarded_by_user(): void
    {
        $admin  = $this->createAdmin();
        $member = $this->createMember();

        $award = MeritAward::create([
            'member_id'          => $member->id,
            'awarded_by_user_id' => $admin->id,
            'amount'             => 20,
            'reason'             => 'Test reason',
        ]);

        $this->assertEquals($admin->id, $award->awardedBy->id);
    }

    // -----------------------------------------------------------------------
    // Member merits balance tests
    // -----------------------------------------------------------------------

    public function test_member_starts_with_zero_merits(): void
    {
        $member = $this->createMember();

        $this->assertEquals(0, $member->merits);
    }

    public function test_member_merits_increment_on_award(): void
    {
        $admin  = $this->createAdmin();
        $member = $this->createMember();

        MeritAward::create([
            'member_id'          => $member->id,
            'awarded_by_user_id' => $admin->id,
            'amount'             => 100,
            'reason'             => 'Test',
        ]);
        $member->increment('merits', 100);

        $member->refresh();
        $this->assertEquals(100, $member->merits);
    }

    public function test_member_merits_decrement_on_redemption(): void
    {
        $admin    = $this->createAdmin();
        $member   = $this->createMember(['merits' => 100]);
        $category = RewardCategory::create(['name' => 'In-Game Items', 'sort_order' => 1]);
        $reward   = Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Ship Skin',
            'merit_cost'         => 50,
            'sort_order'         => 1,
        ]);

        MeritRedemption::create([
            'member_id'           => $member->id,
            'reward_id'           => $reward->id,
            'merit_cost'          => $reward->merit_cost,
            'redeemed_by_user_id' => $admin->id,
        ]);
        $member->decrement('merits', $reward->merit_cost);

        $member->refresh();
        $this->assertEquals(50, $member->merits);
    }

    public function test_member_has_merit_awards_relationship(): void
    {
        $admin  = $this->createAdmin();
        $member = $this->createMember();

        MeritAward::create([
            'member_id'          => $member->id,
            'awarded_by_user_id' => $admin->id,
            'amount'             => 25,
            'reason'             => 'First award',
        ]);
        MeritAward::create([
            'member_id'          => $member->id,
            'awarded_by_user_id' => $admin->id,
            'amount'             => 30,
            'reason'             => 'Second award',
        ]);

        $this->assertCount(2, $member->meritAwards);
    }

    public function test_member_has_merit_redemptions_relationship(): void
    {
        $admin    = $this->createAdmin();
        $member   = $this->createMember(['merits' => 200]);
        $category = RewardCategory::create(['name' => 'Cosmetics', 'sort_order' => 1]);
        $reward   = Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Badge',
            'merit_cost'         => 75,
            'sort_order'         => 1,
        ]);

        MeritRedemption::create([
            'member_id'           => $member->id,
            'reward_id'           => $reward->id,
            'merit_cost'          => 75,
            'redeemed_by_user_id' => $admin->id,
        ]);

        $this->assertCount(1, $member->meritRedemptions);
    }

    // -----------------------------------------------------------------------
    // RewardCategory model tests
    // -----------------------------------------------------------------------

    public function test_reward_category_can_be_created(): void
    {
        RewardCategory::create(['name' => 'In-Game Items', 'sort_order' => 1]);

        $this->assertDatabaseHas('reward_categories', ['name' => 'In-Game Items']);
    }

    public function test_reward_category_has_rewards_relationship(): void
    {
        $category = RewardCategory::create(['name' => 'Cosmetics', 'sort_order' => 1]);

        Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Gold Badge',
            'merit_cost'         => 100,
            'sort_order'         => 1,
        ]);

        $this->assertCount(1, $category->rewards);
    }

    // -----------------------------------------------------------------------
    // Reward model tests
    // -----------------------------------------------------------------------

    public function test_reward_can_be_created_with_merit_cost(): void
    {
        $category = RewardCategory::create(['name' => 'Exclusive', 'sort_order' => 1]);

        $reward = Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Custom Ship Skin',
            'description'        => 'A rare cosmetic item',
            'merit_cost'         => 250,
            'sort_order'         => 1,
        ]);

        $this->assertDatabaseHas('rewards', [
            'name'       => 'Custom Ship Skin',
            'merit_cost' => 250,
        ]);
        $this->assertEquals($category->id, $reward->category->id);
    }

    // -----------------------------------------------------------------------
    // MeritRedemption model tests
    // -----------------------------------------------------------------------

    public function test_merit_redemption_records_reward_and_cost(): void
    {
        $admin    = $this->createAdmin();
        $member   = $this->createMember(['merits' => 300]);
        $category = RewardCategory::create(['name' => 'Titles', 'sort_order' => 1]);
        $reward   = Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Fleet Admiral Title',
            'merit_cost'         => 200,
            'sort_order'         => 1,
        ]);

        $redemption = MeritRedemption::create([
            'member_id'           => $member->id,
            'reward_id'           => $reward->id,
            'merit_cost'          => $reward->merit_cost,
            'redeemed_by_user_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('merit_redemptions', [
            'member_id'  => $member->id,
            'reward_id'  => $reward->id,
            'merit_cost' => 200,
        ]);
        $this->assertEquals($member->id, $redemption->member->id);
        $this->assertEquals($reward->id, $redemption->reward->id);
        $this->assertEquals($admin->id, $redemption->redeemedBy->id);
    }

    // -----------------------------------------------------------------------
    // History tracking tests
    // -----------------------------------------------------------------------

    public function test_merit_award_history_is_preserved(): void
    {
        $admin   = $this->createAdmin();
        $member  = $this->createMember();
        $reasons = ['Won PvP tournament', 'Best miner of the month', 'Exceptional roleplay'];
        $amounts = [50, 75, 30];

        foreach (array_keys($reasons) as $i) {
            MeritAward::create([
                'member_id'          => $member->id,
                'awarded_by_user_id' => $admin->id,
                'amount'             => $amounts[$i],
                'reason'             => $reasons[$i],
            ]);
        }

        $this->assertDatabaseCount('merit_awards', 3);

        $awards = MeritAward::where('member_id', $member->id)->get();
        $this->assertEquals('Won PvP tournament', $awards[0]->reason);
        $this->assertEquals(50, $awards[0]->amount);
    }

    public function test_redemption_history_is_preserved(): void
    {
        $admin    = $this->createAdmin();
        $member   = $this->createMember(['merits' => 500]);
        $category = RewardCategory::create(['name' => 'Rewards', 'sort_order' => 1]);

        $reward1 = Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Item A',
            'merit_cost'         => 100,
            'sort_order'         => 1,
        ]);
        $reward2 = Reward::create([
            'reward_category_id' => $category->id,
            'name'               => 'Item B',
            'merit_cost'         => 150,
            'sort_order'         => 2,
        ]);

        MeritRedemption::create([
            'member_id'           => $member->id,
            'reward_id'           => $reward1->id,
            'merit_cost'          => 100,
            'redeemed_by_user_id' => $admin->id,
        ]);
        MeritRedemption::create([
            'member_id'           => $member->id,
            'reward_id'           => $reward2->id,
            'merit_cost'          => 150,
            'redeemed_by_user_id' => $admin->id,
        ]);

        $this->assertDatabaseCount('merit_redemptions', 2);

        $redemptions = MeritRedemption::where('member_id', $member->id)->get();
        $this->assertEquals('Item A', $redemptions[0]->reward->name);
        $this->assertEquals(100, $redemptions[0]->merit_cost);
    }
}
