<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TicketStatusUpdated;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationClearTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function notifyUser(User $user, string $message): void
    {
        $project = Project::create(['name' => 'Addis Ring Road', 'discipline' => 'Infrastructure', 'status' => 'active']);
        $requester = User::factory()->create();
        $requester->assignRole('user');
        $travelRequest = TravelRequest::create([
            'user_id' => $requester->id,
            'project_id' => $project->id,
            'destination' => 'Hawassa',
            'origin' => 'Addis Ababa',
            'passenger_count' => 1,
            'flight_type' => 'national',
            'travel_date' => '2026-09-20',
            'purpose' => 'Site visit',
            'status' => 'approved',
        ]);

        $user->notify(new TicketStatusUpdated($travelRequest, $message, 'info'));
    }

    public function test_clear_read_removes_only_read_notifications(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->notifyUser($user, 'First notification');
        $this->notifyUser($user, 'Second notification');
        $this->assertSame(2, $user->notifications()->count());

        // Mark the newest one as read; the older stays unread.
        $user->notifications()->latest()->first()->markAsRead();
        $this->assertSame(1, $user->notifications()->whereNotNull('read_at')->count());
        $this->assertSame(1, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('notifications.clearRead'));

        $response->assertRedirect();
        $this->assertSame(0, $user->notifications()->whereNotNull('read_at')->count());
        $this->assertSame(1, $user->notifications()->count(), 'Unread notifications must be kept.');
        $this->assertSame(1, $user->unreadNotifications()->count());
    }

    public function test_clear_read_with_no_read_notifications_is_a_noop(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->notifyUser($user, 'Only unread notification');

        $response = $this->actingAs($user)->post(route('notifications.clearRead'));

        $response->assertRedirect();
        $this->assertSame(1, $user->notifications()->count());
        $this->assertSame(1, $user->unreadNotifications()->count());
    }
}
