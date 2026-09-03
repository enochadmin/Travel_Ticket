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
            'password' => 'password123',
            'password_confirmation' => 'password123',
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

    public function test_email_is_accepted_and_stored_in_lowercase(): void
    {
        $response = $this->post('/register', [
            'name' => 'Abebe Kebede Alemu',
            'email' => '  Abebe.Kebede@Example.COM ',
            'project_name' => 'Addis Expressway',
            'role' => 'user',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status', "Please contact your Organization's IT Admin to Access the System");

        $this->assertDatabaseHas('user_registrations', [
            'email' => 'abebe.kebede@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_email_without_at_or_com_suffix_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Abebe Kebede Alemu',
            'email' => 'invalid-email',
            'project_name' => 'Addis Expressway',
            'role' => 'user',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');

        $emailErrors = session('errors')->get('email');
        $this->assertTrue(
            collect($emailErrors)->contains(
                fn (string $message) => str_contains($message, 'must contain "@" and end with ".com"')
            ),
            'Expected the "@" and ".com" format message, got: '.implode(' | ', $emailErrors)
        );

        $this->assertDatabaseMissing('user_registrations', [
            'email' => 'invalid-email',
        ]);
    }

    public function test_email_that_already_has_a_pending_registration_cannot_be_duplicated(): void
    {
        UserRegistration::create([
            'name' => 'First Applicant',
            'email' => 'duplicate@example.com',
            'project_name' => 'Addis Expressway',
            'role' => 'user',
            'status' => 'pending',
        ]);

        $response = $this->post('/register', [
            'name' => 'Second Applicant',
            'email' => 'DUPLICATE@example.com',
            'project_name' => 'Addis Expressway',
            'role' => 'user',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'already been used to request access and cannot be duplicated',
            session('errors')->first('email')
        );

        $this->assertDatabaseCount('user_registrations', 1);
    }

    public function test_email_of_an_existing_user_cannot_be_registered_again(): void
    {
        User::factory()->create([
            'email' => 'taken@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'New Applicant',
            'email' => 'taken@example.com',
            'project_name' => 'Addis Expressway',
            'role' => 'user',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'already registered and cannot be duplicated',
            session('errors')->first('email')
        );

        $this->assertDatabaseCount('user_registrations', 0);
    }
}
