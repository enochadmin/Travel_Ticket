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
        $roles = ['admin', 'user', 'project-manager', 'head-office-director', 'commercial-director', 'ceo'];
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
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
