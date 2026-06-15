<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $this->ensureDefaultPermissions();

        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(15);

        return view('settings.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->ensureDefaultPermissions();

        return view('settings.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRole($request);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('settings.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = $this->ensureDefaultPermissions();
        $role->load('permissions');

        return view('settings.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $this->validateRole($request, $role);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('settings.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('settings.roles.index')->with('error', 'The admin role cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return redirect()->route('settings.roles.index')->with('error', 'This role is assigned to users and cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('settings.roles.index')->with('success', 'Role deleted successfully.');
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('roles', 'name')->ignore($role?->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ], [
            'name.regex' => 'Use lowercase letters, numbers, and hyphens only.',
        ]);
    }

    private function ensureDefaultPermissions()
    {
        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
            'travel-requests.view',
            'travel-requests.create',
            'travel-requests.approve',
            'travel-requests.reject',
            'reports.view',
            'settings.view',
            'settings.update',
            'reception.view',
            'reception.process-tickets',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        return Permission::orderBy('name')->get();
    }
}
