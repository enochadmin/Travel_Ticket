<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPendingBreakdownTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function director(): User
    {
        $user = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $user->assignRole('commercial-director');

        return $user;
    }

    private function project(string $name): Project
    {
        return Project::create(['name' => $name, 'status' => 'active']);
    }

    private function ticket(User $requester, Project $project, string $status, string $travelDate, ?string $createdAt = null): TravelRequest
    {
        $ticket = TravelRequest::create([
            'user_id' => $requester->id,
            'project_id' => $project->id,
            'destination' => 'Dubai',
            'origin' => 'Addis Ababa',
            'passenger_count' => 1,
            'flight_type' => 'international',
            'travel_date' => $travelDate,
            'purpose' => 'Site visit',
            'status' => $status,
        ]);

        if ($createdAt !== null) {
            $ticket->forceFill(['created_at' => $createdAt])->save();
        }

        return $ticket;
    }

    public function test_pending_panel_is_rendered_for_the_commercial_director(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');
        $beta = $this->project('Beta');

        $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(5)->toDateString());
        $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(9)->toDateString());
        $this->ticket($requester, $beta, 'pending_pm', now()->addDays(3)->toDateString());
        $this->ticket($requester, $beta, 'approved', now()->addDays(3)->toDateString());

        $response = $this->actingAs($director)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Commercial review queue');
        $response->assertSee('Pending by project');

        // The filter row is collapsed to a single line until the user opens it.
        $response->assertSee('Filter by date & project');
        $this->assertFalse($response->viewData('pendingFiltersActive'));

        $byProject = $response->viewData('pendingProjects');

        // Only the commercial queue is counted — the PM ticket and the approved one are not.
        $this->assertSame(2, $response->viewData('pendingTotal'));
        $this->assertCount(1, $byProject);
        $this->assertSame('Alpha', $byProject[0]['name']);
        $this->assertSame(2, $byProject[0]['total']);

        // No per-stage filtering for the commercial director.
        $response->assertDontSee('name="pending_stage"', false);
        $response->assertDontSee('PM review');
        $response->assertDontSee('CEO review');
    }

    public function test_pending_can_be_filtered_by_project(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');
        $beta = $this->project('Beta');

        $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(5)->toDateString());
        $this->ticket($requester, $beta, 'pending_commercial', now()->addDays(5)->toDateString());
        $this->ticket($requester, $beta, 'pending_pm', now()->addDays(5)->toDateString());

        $response = $this->actingAs($director)->get(route('dashboard', ['pending_project' => $beta->id]));

        $response->assertOk();

        $byProject = $response->viewData('pendingProjects');

        $this->assertCount(1, $byProject);
        $this->assertSame('Beta', $byProject[0]['name']);
        $this->assertSame(1, $byProject[0]['total']);
        $this->assertSame(1, $response->viewData('pendingTotal'));
        $this->assertTrue($response->viewData('pendingViewOpen'));

        // Applying a filter opens the filter row and summarises it on the single line.
        $this->assertTrue($response->viewData('pendingFiltersActive'));
        $response->assertSee('Hide filters');
        $response->assertSee('Beta');
    }

    public function test_pending_can_be_filtered_by_date_range(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');

        $this->ticket($requester, $alpha, 'pending_commercial', '2026-01-10');
        $this->ticket($requester, $alpha, 'pending_commercial', '2026-03-10');

        $response = $this->actingAs($director)->get(route('dashboard', [
            'pending_from' => '2026-03-01',
            'pending_to' => '2026-03-31',
        ]));

        $response->assertOk();
        $this->assertSame(1, $response->viewData('pendingTotal'));
        $this->assertSame(1, $response->viewData('pendingProjects')[0]['total']);
    }

    public function test_pending_can_be_filtered_by_request_date_instead_of_travel_date(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');

        $this->ticket($requester, $alpha, 'pending_commercial', '2026-01-10', '2026-05-02 10:00:00');
        $this->ticket($requester, $alpha, 'pending_commercial', '2026-06-10', '2026-06-02 10:00:00');

        $response = $this->actingAs($director)->get(route('dashboard', [
            'pending_date_type' => 'created_at',
            'pending_from' => '2026-05-01',
            'pending_to' => '2026-05-31',
        ]));

        $response->assertOk();
        $this->assertSame(1, $response->viewData('pendingTotal'));
    }

    public function test_ticket_age_is_bucketed_and_projects_are_sorted_oldest_first(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');
        $beta = $this->project('Beta');

        $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(5)->toDateString(), now()->subDays(20)->toDateTimeString());
        $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(5)->toDateString(), now()->subDays(12)->toDateTimeString());
        $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(5)->toDateString(), now()->subDays(3)->toDateTimeString());
        $this->ticket($requester, $beta, 'pending_commercial', now()->addDays(5)->toDateString(), now()->subDays(18)->toDateTimeString());

        $response = $this->actingAs($director)->get(route('dashboard'));

        $aging = $response->viewData('pendingAging');

        $this->assertSame(2, $aging['critical']);
        $this->assertSame(1, $aging['watch']);
        $this->assertSame(1, $aging['fresh']);
        $this->assertSame(20, $aging['oldest_days']);
        $this->assertSame(13, $aging['avg_days']);

        $projects = $response->viewData('pendingProjects');

        // Oldest project first, so the worst queue is what the director sees.
        $this->assertSame('Alpha', $projects[0]['name']);
        $this->assertSame(20, $projects[0]['oldest_days']);
        $this->assertSame(1, $projects[0]['buckets']['critical']);
        $this->assertSame(1, $projects[0]['buckets']['watch']);
        $this->assertSame(1, $projects[0]['buckets']['fresh']);
        $this->assertSame('Beta', $projects[1]['name']);
        $this->assertSame(1, $projects[1]['buckets']['critical']);
        $this->assertNotNull($projects[0]['oldest_requested_at']);
    }

    public function test_age_bucket_boundaries_match_the_fifteen_and_ten_day_rules(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');

        foreach ([16, 15, 10, 9] as $days) {
            $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(5)->toDateString(), now()->subDays($days)->toDateTimeString());
        }

        $aging = $this->actingAs($director)->get(route('dashboard'))->viewData('pendingAging');

        // 16 days is critical, 15 and 10 are still in the warning band, 9 is healthy.
        $this->assertSame(1, $aging['critical']);
        $this->assertSame(2, $aging['watch']);
        $this->assertSame(1, $aging['fresh']);
    }

    public function test_other_pending_stages_are_never_counted(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');

        $this->ticket($requester, $alpha, 'pending_pm', now()->addDays(4)->toDateString());
        $this->ticket($requester, $alpha, 'pending_hod', now()->addDays(4)->toDateString());
        $this->ticket($requester, $alpha, 'pending_ceo', now()->addDays(4)->toDateString());

        $response = $this->actingAs($director)->get(route('dashboard'));

        $response->assertOk();
        $this->assertSame(0, $response->viewData('pendingTotal'));
        $this->assertSame(0, $response->viewData('pendingGrandTotal'));
        $this->assertTrue($response->viewData('pendingProjects')->isEmpty());
        $response->assertSee('Nothing pending for this filter');
    }

    public function test_a_bogus_stage_parameter_is_ignored(): void
    {
        $director = $this->director();
        $requester = User::factory()->create(['status' => 'active']);
        $alpha = $this->project('Alpha');

        $this->ticket($requester, $alpha, 'pending_commercial', now()->addDays(4)->toDateString());

        $response = $this->actingAs($director)->get(route('dashboard', ['pending_stage' => 'pending_ceo']));

        $response->assertOk();
        $this->assertSame(1, $response->viewData('pendingTotal'));
    }

    public function test_other_roles_do_not_see_the_pending_panel(): void
    {
        $user = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Pending by project');
    }
}
