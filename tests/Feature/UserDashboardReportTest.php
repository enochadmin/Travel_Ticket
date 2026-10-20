<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function requester(): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('user');

        return $user;
    }

    private function project(string $name = 'Alpha'): Project
    {
        return Project::create(['name' => $name, 'status' => 'active']);
    }

    private function trip(User $user, Project $project, string $status, string $travelDate, string $destination): TravelRequest
    {
        return TravelRequest::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'destination' => $destination,
            'origin' => 'Addis Ababa',
            'passenger_count' => 1,
            'flight_type' => 'international',
            'travel_date' => $travelDate,
            'purpose' => 'Client visit',
            'status' => $status,
        ]);
    }

    public function test_the_report_uses_only_the_signed_in_users_approved_trips(): void
    {
        $user = $this->requester();
        $colleague = $this->requester();
        $alpha = $this->project('Alpha');

        $this->trip($user, $alpha, 'approved', now()->subMonths(2)->toDateString(), 'Nairobi');
        $this->trip($user, $alpha, 'approved', now()->subMonths(2)->toDateString(), 'Nairobi');
        $this->trip($user, $alpha, 'approved', now()->subMonth()->toDateString(), 'Dubai');
        $this->trip($user, $alpha, 'pending_commercial', now()->toDateString(), 'Cairo');
        $this->trip($user, $alpha, 'rejected', now()->subDays(5)->toDateString(), 'Mombasa');
        $this->trip($colleague, $alpha, 'approved', now()->toDateString(), 'Lagos');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $trend = $response->viewData('travelTrend');
        $this->assertCount(12, $trend);
        $this->assertSame(3, (int) $trend->sum('count'));

        $twoMonthsAgo = $trend->firstWhere('period', now()->subMonths(2)->format('M Y'));
        $this->assertSame(2, $twoMonthsAgo['count']);

        $destinations = $response->viewData('topDestinations');
        $this->assertSame('Nairobi', $destinations[0]['name']);
        $this->assertSame(2, $destinations[0]['count']);
        $this->assertSame('Dubai', $destinations[1]['name']);
        // Pending, rejected and other people's tickets never make the report.
        $this->assertNotContains('Cairo', $destinations->pluck('name'));
        $this->assertNotContains('Mombasa', $destinations->pluck('name'));
        $this->assertNotContains('Lagos', $destinations->pluck('name'));

        $projectShare = $response->viewData('projectShare');
        $this->assertSame('Alpha', $projectShare[0]['name']);
        $this->assertSame(3, $projectShare[0]['count']);

        $response->assertSee('My travel trend');
        $response->assertSee('Where you travel most');
        $response->assertSee('Latest travel history');
    }

    public function test_last_trip_and_next_upcoming_trip_are_picked_from_approved_trips(): void
    {
        $user = $this->requester();
        $alpha = $this->project('Alpha');

        $this->trip($user, $alpha, 'approved', now()->subMonths(4)->toDateString(), 'Older trip');
        $this->trip($user, $alpha, 'approved', now()->subDays(10)->toDateString(), 'Most recent trip');
        $this->trip($user, $alpha, 'approved', now()->addDays(3)->toDateString(), 'Sooner trip');
        $this->trip($user, $alpha, 'approved', now()->addDays(10)->toDateString(), 'Later trip');
        $this->trip($user, $alpha, 'pending_pm', now()->addDays(1)->toDateString(), 'Not approved yet');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $this->assertSame('Most recent trip', $response->viewData('latestPastTrip')->destination);
        $this->assertSame('Sooner trip', $response->viewData('nextUpcomingTrip')->destination);

        $response->assertSee('Most recent trip');
        $response->assertSee('Sooner trip');
        $response->assertSee('In 3 days');
    }

    public function test_a_user_without_approved_trips_sees_empty_states(): void
    {
        $user = $this->requester();
        $alpha = $this->project('Alpha');

        $this->trip($user, $alpha, 'pending_pm', now()->addDays(2)->toDateString(), 'Cairo');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $this->assertNull($response->viewData('latestPastTrip'));
        $this->assertNull($response->viewData('nextUpcomingTrip'));
        $this->assertTrue($response->viewData('topDestinations')->isEmpty());
        $this->assertSame(0, (int) $response->viewData('travelTrendTotal'));

        $response->assertSee('No approved trips yet.');
    }

    public function test_the_report_is_not_shown_to_other_roles(): void
    {
        $director = User::factory()->create(['status' => 'active']);
        $director->assignRole('commercial-director');

        $this->actingAs($director)->get(route('dashboard'))
            ->assertDontSee('My travel trend');
    }
}
