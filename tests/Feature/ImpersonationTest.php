<?php

namespace Tests\Feature;

use App\Models\ImpersonationLog;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeUser(string $name, string $email, string $role): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
        ]);
        $user->assignRole($role);

        return $user;
    }

    public function test_admin_can_open_a_user_read_only_and_exit_back_to_admin(): void
    {
        $admin = $this->makeUser('Boss Admin', 'boss@example.com', 'admin');
        $target = $this->makeUser('Hanna User', 'hanna@example.com', 'user');

        $start = $this->actingAs($admin)->post(route('impersonation.start', $target));

        $start->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($target);
        $this->assertSame($admin->id, session('impersonator_id'));
        $this->assertSame($target->id, session('impersonated_id'));

        // The target's own view is rendered with the read-only banner.
        $dashboard = $this->get(route('dashboard'));
        $dashboard->assertStatus(200);
        $dashboard->assertSee('Hanna User');
        $dashboard->assertSee('read-only preview');

        // Mutations are blocked while impersonating (view-only mode).
        $blocked = $this->post(route('notifications.markAllRead'));
        $blocked->assertRedirect();
        $blocked->assertSessionHas('error');
        $this->assertAuthenticatedAs($target);

        // Exit restores the admin.
        $exit = $this->post(route('impersonation.exit'));
        $exit->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('impersonator_id'));
        $this->assertNull(session('impersonated_id'));

        // Audit trail recorded both events.
        $this->assertDatabaseHas('impersonation_logs', [
            'admin_id' => $admin->id,
            'target_id' => $target->id,
            'action' => 'start',
        ]);
        $this->assertDatabaseHas('impersonation_logs', [
            'admin_id' => $admin->id,
            'target_id' => $target->id,
            'action' => 'exit',
        ]);
    }

    public function test_admin_cannot_open_own_account_or_another_admin(): void
    {
        $admin = $this->makeUser('Boss Admin', 'boss@example.com', 'admin');
        $otherAdmin = $this->makeUser('Second Admin', 'second@example.com', 'admin');

        $this->actingAs($admin)->post(route('impersonation.start', $admin))->assertForbidden();
        $this->actingAs($admin)->post(route('impersonation.start', $otherAdmin))->assertForbidden();

        $this->assertDatabaseCount('impersonation_logs', 0);
    }

    public function test_non_admin_cannot_use_open_as(): void
    {
        $pm = $this->makeUser('Pete Manager', 'pete@example.com', 'project-manager');
        $target = $this->makeUser('Hanna User', 'hanna@example.com', 'user');

        $this->actingAs($pm)->post(route('impersonation.start', $target))->assertForbidden();
        $this->assertNull(session('impersonated_id'));
    }

    public function test_admin_can_view_the_impersonation_log(): void
    {
        $admin = $this->makeUser('Boss Admin', 'boss@example.com', 'admin');
        $target = $this->makeUser('Hanna User', 'hanna@example.com', 'user');

        $this->actingAs($admin)->post(route('impersonation.start', $target));
        $this->post(route('impersonation.exit'));

        $response = $this->get(route('settings.impersonation-logs'));
        $response->assertStatus(200);
        $response->assertSee('Boss Admin');
        $response->assertSee('Hanna User');

        $this->assertSame(2, ImpersonationLog::count());
    }

    public function test_logout_while_impersonating_exits_instead_of_logging_out(): void
    {
        $admin = $this->makeUser('Boss Admin', 'boss@example.com', 'admin');
        $target = $this->makeUser('Hanna User', 'hanna@example.com', 'user');

        $this->actingAs($admin)->post(route('impersonation.start', $target));
        $this->assertAuthenticatedAs($target);

        $response = $this->post(route('logout'));
        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('impersonator_id'));
        $this->assertDatabaseHas('impersonation_logs', [
            'admin_id' => $admin->id,
            'target_id' => $target->id,
            'action' => 'exit',
        ]);
    }
}
