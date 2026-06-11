<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name']) || empty($row['email'])) {
            return null;
        }

        $user = User::firstOrNew(['email' => trim($row['email'])]);
        $isNew = ! $user->exists;

        $user->name = trim($row['name']);
        $user->project_id = $this->nullableInt($row['project_id'] ?? null);

        if ($isNew) {
            $user->password = 'password';
            $user->must_change_password = true;
        }

        $user->save();

        $role = $this->resolveRole($row);
        if ($role) {
            $user->syncRoles([$role->name]);
        }

        if ($user->project_id) {
            $user->projects()->syncWithoutDetaching([$user->project_id]);
        }

        return $user;
    }

    private function resolveRole(array $row): ?Role
    {
        if (! empty($row['role_id'])) {
            return Role::find((int) $row['role_id']);
        }

        if (! empty($row['role'])) {
            return Role::where('name', trim($row['role']))->first();
        }

        return null;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
