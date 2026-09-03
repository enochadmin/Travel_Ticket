<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeadOfficeReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeUser(string $name, string $email, string $role, ?int $projectId = null): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'project_id' => $projectId,
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function makeTravelRequest(User $requester, Project $project, string $status = 'pending_pm'): TravelRequest
    {
        return TravelRequest::create([
            'user_id' => $requester->id,
            'project_id' => $project->id,
            'destination' => 'Bahir Dar',
            'origin' => 'Addis Ababa',
            'passenger_count' => 1,
            'flight_type' => 'national',
            'travel_date' => '2026-09-15',
            'purpose' => 'Field supervision visit',
            'status' => $status,
        ]);
    }

    public function test_discipline_flags_project_as_head_office(): void
    {
        $hoDept = Project::create(['name' => 'Corporate Finance', 'discipline' => 'Head-Office', 'status' => 'active']);
        $normal = Project::create(['name' => 'Addis Ring Road', 'discipline' => 'Infrastructure', 'status' => 'active']);
        // Legacy: project created before the discipline flag, name mentions head office.
        $legacy = Project::create(['name' => 'Head Office HR', 'discipline' => 'Building', 'status' => 'active']);

        $this->assertTrue($hoDept->isHeadOffice());
        $this->assertFalse($normal->isHeadOffice());
        $this->assertTrue($legacy->isHeadOffice());

        $headOfficeIds = Project::headOffice()->pluck('id');
        $this->assertTrue($headOfficeIds->contains($hoDept->id));
        $this->assertTrue($headOfficeIds->contains($legacy->id));
        $this->assertFalse($headOfficeIds->contains($normal->id));
    }

    public function test_head_office_manager_appears_in_project_manager_selection(): void
    {
        $hoManager = $this->makeUser('Dawit HO Manager', 'dawit.ho@example.com', 'head-office-manager');
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('projects.create'));

        $response->assertStatus(200);
        $response->assertSee($hoManager->name);
    }

    public function test_head_office_manager_sees_requests_of_managed_department(): void
    {
        $hoManager = $this->makeUser('Dawit HO Manager', 'dawit.ho@example.com', 'head-office-manager');
        $member = $this->makeUser('Hanna Member', 'hanna@example.com', 'user');

        $hoDept = Project::create([
            'name' => 'Corporate Finance',
            'discipline' => 'Head-Office',
            'status' => 'active',
            'manager_id' => $hoManager->id,
        ]);
        $normal = Project::create(['name' => 'Addis Ring Road', 'discipline' => 'Infrastructure', 'status' => 'active']);

        $member->projects()->attach($hoDept->id);
        $member->update(['project_id' => $hoDept->id]);

        $hoRequest = $this->makeTravelRequest($member, $hoDept);
        $hoRequest->update(['destination' => 'Gondar']);
        $fieldRequest = $this->makeTravelRequest($member, $normal);
        $fieldRequest->update(['destination' => 'Dire Dawa']);

        $response = $this->actingAs($hoManager)->get(route('travel-requests.index'));

        $response->assertStatus(200);
        $content = $response->getContent();
        // Only the managed head-office department's request is listed.
        $this->assertStringContainsString('Gondar', $content);
        $this->assertStringNotContainsString('Dire Dawa', $content);
    }

    public function test_head_office_manager_can_approve_department_request_like_a_project_manager(): void
    {
        $hoManager = $this->makeUser('Dawit HO Manager', 'dawit.ho@example.com', 'head-office-manager');
        $member = $this->makeUser('Hanna Member', 'hanna@example.com', 'user');

        $hoDept = Project::create([
            'name' => 'Corporate Finance',
            'discipline' => 'Head-Office',
            'status' => 'active',
            'manager_id' => $hoManager->id,
        ]);
        $member->projects()->attach($hoDept->id);
        $member->update(['project_id' => $hoDept->id]);

        $request = $this->makeTravelRequest($member, $hoDept, 'pending_pm');

        $response = $this->actingAs($hoManager)
            ->patch(route('travel-requests.approve', $request));

        $response->assertRedirect();
        $this->assertSame('pending_commercial', $request->fresh()->status);
        $this->assertSame($hoManager->id, $request->fresh()->pm_id);
        $this->assertNotNull($request->fresh()->pm_approved_at);
    }

    public function test_commercial_director_can_filter_records_to_head_office_only(): void
    {
        $cd = $this->makeUser('CD User', 'cd@example.com', 'commercial-director');
        $member = $this->makeUser('Meron Member', 'meron@example.com', 'user');

        $hoDept = Project::create(['name' => 'Corporate Finance', 'discipline' => 'Head-Office', 'status' => 'active']);
        $normal = Project::create(['name' => 'Addis Ring Road', 'discipline' => 'Infrastructure', 'status' => 'active']);

        $hoRequest = $this->makeTravelRequest($member, $hoDept, 'approved');
        $hoRequest->update(['destination' => 'Gondar']);

        $fieldRequest = $this->makeTravelRequest($member, $normal, 'approved');
        $fieldRequest->update(['destination' => 'Dire Dawa']);

        $response = $this->actingAs($cd)
            ->get(route('travel-requests.index', ['status' => 'approved', 'head_office_only' => 1]));

        $response->assertStatus(200);
        $content = $response->getContent();

        // The head-office department record is listed (destination shown in the row)...
        $this->assertStringContainsString('Gondar', $content);
        // ...while the non-head-office project record is filtered out of the table.
        $this->assertStringNotContainsString('Dire Dawa', $content);
    }
}
