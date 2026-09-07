<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectListFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create(['name' => 'Filter Admin', 'email' => 'filter-admin@example.com']);
        $admin->assignRole('admin');

        Project::create(['name' => 'Alpha Road', 'project_code' => 'ALPHA-1', 'discipline' => 'Infrastructure', 'location' => 'Addis Ababa', 'status' => 'active']);
        Project::create(['name' => 'Head Office Finance', 'project_code' => 'HOF-1', 'discipline' => 'Head-Office', 'location' => 'Addis Ababa', 'status' => 'completed']);
        Project::create(['name' => 'Beta Water', 'project_code' => 'BETA-1', 'discipline' => 'Water', 'location' => 'Hawassa', 'status' => 'on-hold']);

        $this->actingAs($admin);
    }

    public function test_index_lists_all_projects(): void
    {
        $response = $this->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee('Alpha Road');
        $response->assertSee('Head Office Finance');
        $response->assertSee('Beta Water');
    }

    public function test_status_tab_filters_projects(): void
    {
        $response = $this->get(route('projects.index', ['status' => 'completed']));

        $response->assertStatus(200);
        $response->assertSee('Head Office Finance');
        $response->assertDontSee('Alpha Road');
        $response->assertDontSee('Beta Water');
    }

    public function test_head_office_tab_uses_discipline_flag(): void
    {
        $response = $this->get(route('projects.index', ['status' => 'head-office']));

        $response->assertStatus(200);
        $response->assertSee('Head Office Finance');
        $response->assertDontSee('Alpha Road');
        $response->assertDontSee('Beta Water');
    }

    public function test_name_search_filters_projects(): void
    {
        $response = $this->get(route('projects.index', ['search' => 'Alpha']));

        $response->assertStatus(200);
        $response->assertSee('Alpha Road');
        $response->assertDontSee('Head Office Finance');
        $response->assertDontSee('Beta Water');
    }

    public function test_search_and_status_can_be_combined(): void
    {
        $response = $this->get(route('projects.index', ['search' => 'Office', 'status' => 'completed']));

        $response->assertStatus(200);
        $response->assertSee('Head Office Finance');
        $response->assertDontSee('Alpha Road');
    }
}
