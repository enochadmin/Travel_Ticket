<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Project;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Exports\UsersTemplateExport;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'project'])->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $projects = Project::all();
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => 'password',
            'project_id' => $validated['project_id'],
            'must_change_password' => true,
        ]);

        $user->assignRole($validated['role']);

        if (!empty($validated['project_id'])) {
            $user->projects()->syncWithoutDetaching([$validated['project_id']]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $projects = Project::all();

        return view('users.edit', compact('user', 'roles', 'projects'));
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

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'project_id' => $validated['project_id'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = $validated['password'];
            $userData['must_change_password'] = false;
        }

        $user->update($userData);

        // Sync role (removes old, adds new)
        $user->syncRoles([$validated['role']]);

        if (!empty($validated['project_id'])) {
            $user->projects()->syncWithoutDetaching([$validated['project_id']]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the currently logged-in admin or a super-admin (if implemented)
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
}
