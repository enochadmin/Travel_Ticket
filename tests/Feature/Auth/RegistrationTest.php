<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserRegistration;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_submit_registration_for_approval(): void
    {
        $response = $this->post('/register', [
            'name' => 'Abebe Kebede Alemu',
            'email' => 'test@example.com',
            'project_name' => 'Addis Expressway',
            'role' => 'user',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('register'));
        $response->assertSessionHas('status', "Please contact your Organization's IT Admin to Access the System");

        $this->assertDatabaseHas('user_registrations', [
            'email' => 'test@example.com',
            'project_name' => 'Addis Expressway',
            'role' => 'user',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_registration_with_default_password(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();

        $registration = UserRegistration::create([
            'name' => 'Abebe Kebede Alemu',
            'email' => 'abebe@example.com',
            'project_name' => 'Addis Expressway',
            'role' => 'project-manager',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('user-registrations.approve', $registration));

        $response->assertRedirect(route('user-registrations.index'));

        $user = User::where('email', 'abebe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue($user->hasRole('project-manager'));
        $this->assertTrue($registration->fresh()->status === 'approved');
    }
}
