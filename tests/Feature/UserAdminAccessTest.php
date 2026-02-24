<?php

namespace Tests\Feature;

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
}
