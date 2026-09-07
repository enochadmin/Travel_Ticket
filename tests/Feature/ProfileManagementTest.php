<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeUser(array $extra = []): User
    {
        $user = User::factory()->create(array_merge([
            'name' => 'Hanna Bekele',
            'email' => 'hanna@example.com',
            'status' => 'active',
        ], $extra));
        $user->assignRole('user');

        return $user;
    }

    public function test_initials_use_first_and_last_name(): void
    {
        $this->assertSame('HB', $this->makeUser()->initials());
        $this->assertSame('A', User::factory()->create(['name' => 'Admin'])->initials());
        $this->assertSame('KD', User::factory()->create(['name' => 'Kidist  Desta'])->initials());
    }

    public function test_user_can_update_phone_and_upload_avatar(): void
    {
        Storage::fake('public');
        $user = $this->makeUser();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Hanna Bekele',
            'email' => 'hanna@example.com',
            'phone' => '+251 91 234 5678',
            'avatar' => UploadedFile::fake()->image('photo.jpg', 300, 300),
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertNull(session('errors'));

        $fresh = $user->fresh();
        $this->assertSame('+251 91 234 5678', $fresh->phone);
        $this->assertNotNull($fresh->avatar_path);
        $this->assertStringStartsWith('avatars/', $fresh->avatar_path);
        Storage::disk('public')->assertExists($fresh->avatar_path);
    }

    public function test_user_can_remove_avatar(): void
    {
        Storage::fake('public');
        $path = 'avatars/photo.jpg';
        Storage::disk('public')->put($path, 'fake-image');
        $user = $this->makeUser(['avatar_path' => $path]);

        $response = $this->actingAs($user)->post(route('profile.avatar.remove'));

        $response->assertRedirect(route('profile.edit'));
        $this->assertNull($user->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_avatar_validation_rejects_non_images(): void
    {
        Storage::fake('public');
        $user = $this->makeUser();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Hanna Bekele',
            'email' => 'hanna@example.com',
            'avatar' => UploadedFile::fake()->create('resume.pdf', 50, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar_path);
    }

    public function test_presence_helpers_report_online_and_inactive(): void
    {
        $online = $this->makeUser(['email' => 'online@example.com', 'last_activity_at' => now()->subMinute()]);
        $this->assertTrue($online->isOnline());
        $this->assertSame('Online', $online->presenceLabel());

        $stale = $this->makeUser(['email' => 'stale@example.com', 'last_activity_at' => now()->subHours(2)]);
        $this->assertFalse($stale->isOnline());
        $this->assertStringStartsWith('Inactive · active', $stale->presenceLabel());

        $never = $this->makeUser(['email' => 'never@example.com', 'last_activity_at' => null]);
        $this->assertFalse($never->isOnline());
        $this->assertSame('Inactive', $never->presenceLabel());
    }

    public function test_last_activity_is_tracked_on_requests(): void
    {
        $user = $this->makeUser(['last_activity_at' => null]);

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();

        $this->assertNotNull($user->fresh()->last_activity_at);
    }
}
