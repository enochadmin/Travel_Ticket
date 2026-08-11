<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Exports\UsersTemplateExport;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'project', 'managedProject']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $projects = Project::with('manager')->orderBy('name')->get();

        return view('users.create', compact('roles', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|exists:roles,name',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $this->guardProjectManagerAssignment($validated['role'], $validated['project_id'] ?? null);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => 'password',
            'project_id' => $validated['project_id'],
            'must_change_password' => true,
        ]);

        $user->assignRole($validated['role']);
        $user->syncPrimaryProjectMembership($validated['project_id'] ?? null);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $projects = Project::with('manager')->orderBy('name')->get();
        $requestedProjectName = UserRegistration::where('user_id', $user->id)
            ->value('project_name');

        return view('users.edit', compact('user', 'roles', 'projects', 'requestedProjectName'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $this->guardProjectManagerAssignment(
            $validated['role'],
            $validated['project_id'] ?? null,
            $user
        );

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'project_id' => $validated['project_id'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = $validated['password'];
            $userData['must_change_password'] = false;
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);
        $user->syncPrimaryProjectMembership($validated['project_id'] ?? null);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:4096',
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return back()->with('success', 'Users imported successfully! New users must log in with the default password "password" and will be prompted to change it.');
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new UsersTemplateExport, 'users_template.xlsx');
    }

    private function guardProjectManagerAssignment(string $role, ?int $projectId, ?User $user = null): void
    {
        if ($role !== 'project-manager' || ! $projectId) {
            return;
        }

        $project = Project::find($projectId);

        if (! $project?->manager_id) {
            return;
        }

        if ($user && (int) $project->manager_id === (int) $user->id) {
            return;
        }

        throw ValidationException::withMessages([
            'project_id' => 'There is a Project manager already assigned.',
        ]);
    }
}
