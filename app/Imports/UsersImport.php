<?php

namespace App\Imports;

use App\Models\Project;
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

        $role = $this->resolveRole($row);
        $projectId = $this->nullableInt($row['project_id'] ?? null);

        if ($this->projectAssignmentBlocked($role?->name, $projectId, $user)) {
            $projectId = null;
        }

        $user->project_id = $projectId;

        if ($isNew) {
            $user->password = 'password';
            $user->must_change_password = true;
        }

        $user->save();

        if ($role) {
            $user->syncRoles([$role->name]);
        }

        if ($user->project_id) {
            $user->projects()->syncWithoutDetaching([$user->project_id]);
        }

        return $user;
    }

    private function projectAssignmentBlocked(?string $roleName, ?int $projectId, User $user): bool
    {
        if ($roleName !== 'project-manager' || ! $projectId) {
            return false;
        }

        $project = Project::find($projectId);

        if (! $project?->manager_id) {
            return false;
        }

        return (int) $project->manager_id !== (int) $user->id;
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
