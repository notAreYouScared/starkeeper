<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\OrgRole;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_access_panel(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->assertTrue($user->canAccessPanel(Panel::make('admin')));
    }

    public function test_non_admin_user_cannot_access_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->assertFalse($user->canAccessPanel(Panel::make('admin')));
    }

    public function test_make_admin_command_grants_access(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->artisan('app:make-admin', ['email' => $user->email])
            ->assertSuccessful()
            ->expectsOutput("Admin access granted to [{$user->email}].");

        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_make_admin_command_is_idempotent(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->artisan('app:make-admin', ['email' => $user->email])
            ->assertSuccessful()
            ->expectsOutput("User [{$user->email}] already has admin access.");
    }

    public function test_make_admin_command_fails_for_unknown_email(): void
    {
        $this->artisan('app:make-admin', ['email' => 'nobody@example.com'])
            ->assertFailed()
            ->expectsOutput('No user found with email: nobody@example.com');
    }

    public function test_discord_user_defaults_to_non_admin(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'password'   => null,
            'is_admin'   => false,
        ]);

        $this->assertFalse($user->canAccessPanel(Panel::make('admin')));
    }

    public function test_user_with_valid_org_role_can_access_member_panel(): void
    {
        $orgRole = OrgRole::create(['name' => 'member', 'label' => 'Member', 'sort_order' => 1]);

        $user = User::factory()->create(['discord_id' => 'discord_abc']);
        Member::create([
            'discord_id'  => 'discord_abc',
            'name'        => 'Test Member',
            'handle'      => 'testmember',
            'org_role_id' => $orgRole->id,
        ]);

        $panel = Panel::make()->id('member');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_user_without_org_role_cannot_access_member_panel(): void
    {
        $user = User::factory()->create(['discord_id' => 'discord_def']);
        Member::create([
            'discord_id'  => 'discord_def',
            'name'        => 'No Role',
            'handle'      => 'norole',
            'org_role_id' => null,
        ]);

        $panel = Panel::make()->id('member');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_user_with_no_member_record_cannot_access_member_panel(): void
    {
        $user = User::factory()->create(['discord_id' => 'discord_ghi']);

        $panel = Panel::make()->id('member');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_user_without_discord_id_cannot_access_member_panel(): void
    {
        $user = User::factory()->create(['discord_id' => null]);

        $panel = Panel::make()->id('member');

        $this->assertFalse($user->canAccessPanel($panel));
    }
}
