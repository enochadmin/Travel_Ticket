<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'user', 'project-manager', 'head-office-manager', 'head-office-director', 'commercial-director', 'ceo', 'reception'];
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        $permissions = ['edit own ticket', 'delete own ticket'];
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign 'edit own ticket' and 'delete own ticket' permissions to user role
        $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->givePermissionTo(['edit own ticket', 'delete own ticket']);
        }

        // Project managers also raise their own tickets, so they get the same self-service permissions.
        $pmRole = \Spatie\Permission\Models\Role::where('name', 'project-manager')->first();
        if ($pmRole) {
            $pmRole->givePermissionTo(['edit own ticket', 'delete own ticket']);
        }

        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin User',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $admin->assignRole('admin');
    }
}
