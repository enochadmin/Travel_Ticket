<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectsExport;
use App\Imports\ProjectsImport;
use App\Exports\ProjectsTemplateExport;

class ProjectController extends Controller
{
    private function canViewProject(User $user, Project $project): bool
    {
        if ($user->hasAnyRole(['admin', 'head-office-director', 'commercial-director', 'ceo'])) {
            return true;
        }

        if ($user->hasRole('project-manager')) {
            return ($project->manager_id === $user->id)
                || $user->projects()->whereKey($project->id)->exists();
        }

        return false;
    }

    private function canManageMembers(User $user, Project $project): bool
    {
        if ($user->hasAnyRole(['admin', 'head-office-director', 'commercial-director', 'ceo'])) {
            return true;
        }

        return $user->hasRole('project-manager') && $project->manager_id === $user->id;
    }

    public function index()
    {
        $projects = Project::with('manager')
            ->withCount([
                'travelRequests as requested_tickets_count',
                'travelRequests as approved_tickets_count' => function ($query) {
                    $query->where('status', 'approved');
                }
            ])
            ->latest()
            ->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $managers = User::role('project-manager')->orderBy('name')->get();
        $availableUsers = User::with(['roles', 'project'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->orderBy('name')
            ->get();

        return view('projects.create', compact('managers', 'availableUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'nullable|string|max:100|unique:projects,project_code',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'discipline' => 'nullable|string|in:Infrastructure,Water,Building,Head-Office',
            'manager_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,on-hold,completed,cancelled',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $project = Project::create(collect($validated)->except('user_ids')->all());
        $memberIds = collect($validated['user_ids'] ?? [])->unique();

        if (!empty($validated['manager_id'])) {
            User::whereKey($validated['manager_id'])->update(['project_id' => $project->id]);
            $memberIds->push((int) $validated['manager_id']);
        }

        $assignableIds = User::whereIn('id', $memberIds->unique()->values())
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->pluck('id');

        if ($assignableIds->isNotEmpty()) {
            $project->members()->syncWithoutDetaching($assignableIds->all());

            User::whereIn('id', $assignableIds)
                ->whereNull('project_id')
                ->update(['project_id' => $project->id]);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $user = auth()->user();

        if (!$this->canViewProject($user, $project)) {
            abort(403, 'Unauthorized access to this project.');
        }

        $project->load(['manager', 'members.roles', 'members.project', 'travelRequests.user']);

        $requestsBase = TravelRequest::query()->where('project_id', $project->id);
        $stats = [
            'total_requests' => (clone $requestsBase)->count(),
            'pending_pm' => (clone $requestsBase)->where('status', 'pending_pm')->count(),
            'pending_commercial' => (clone $requestsBase)->where('status', 'pending_commercial')->count(),
            'approved' => (clone $requestsBase)->where('status', 'approved')->count(),
            'rejected' => (clone $requestsBase)->where('status', 'rejected')->count(),
            'unique_requesters' => (clone $requestsBase)->distinct('user_id')->count('user_id'),
        ];

        $pendingRequests = TravelRequest::with('user')
            ->where('project_id', $project->id)
            ->whereIn('status', ['pending_pm', 'pending_commercial'])
            ->latest()
            ->take(8)
            ->get();

        $canManageMembers = $this->canManageMembers($user, $project);
        $availableUsers = collect();
        if ($canManageMembers) {
            $availableUsers = User::with(['roles', 'project'])
                ->whereDoesntHave('projects', fn($q) => $q->whereKey($project->id))
                ->orderBy('name')
                ->get();
        }

        return view('projects.show', compact('project', 'stats', 'pendingRequests', 'canManageMembers', 'availableUsers'));
    }

    public function edit(Project $project)
    {
        $managers = User::role('project-manager')->orderBy('name')->get();
        return view('projects.edit', compact('project', 'managers'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'nullable|string|max:100|unique:projects,project_code,' . $project->id,
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'discipline' => 'nullable|string|in:Infrastructure,Water,Building',
            'manager_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,on-hold,completed,cancelled',
        ]);

        $project->update($validated);

        if (!empty($validated['manager_id'])) {
            User::whereKey($validated['manager_id'])->update(['project_id' => $project->id]);
            $project->members()->syncWithoutDetaching([$validated['manager_id']]);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function addMembers(Request $request, Project $project)
    {
        $user = auth()->user();
        if (!$this->canManageMembers($user, $project)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $ids = collect($validated['user_ids'])->unique()->values();

        // Safety: do not reassign admin accounts via project members.
        $assignableIds = User::whereIn('id', $ids)
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->pluck('id');

        $project->members()->syncWithoutDetaching($assignableIds->all());

        User::whereIn('id', $assignableIds)
            ->whereNull('project_id')
            ->update(['project_id' => $project->id]);

        return back()->with('success', 'Members added successfully.');
    }

    public function removeMember(Project $project, User $user)
    {
        $actor = auth()->user();
        if (!$this->canManageMembers($actor, $project)) {
            abort(403, 'Unauthorized action.');
        }

        if (!$user->projects()->whereKey($project->id)->exists()) {
            abort(404);
        }

        if ((int) $user->id === (int) $project->manager_id) {
            return back()->with('error', 'Cannot remove the assigned Project Manager. Change the manager first.');
        }

        $project->members()->detach($user->id);

        if ((int) $user->project_id === (int) $project->id) {
            $user->update(['project_id' => null]);
        }

        return back()->with('success', 'Member removed successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:4096',
        ]);

        Excel::import(new ProjectsImport, $request->file('file'));

        return back()->with('success', 'Projects imported successfully!');
    }

    public function export()
    {
        return Excel::download(new ProjectsExport, 'projects.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProjectsTemplateExport, 'projects_template.xlsx');
    }
}
