<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::with('roles', 'project')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role ID',
            'Role Name',
            'Project ID',
            'Assigned Project',
            'Must Change Password',
            'Created At',
        ];
    }

    public function map($user): array
    {
        $role = $user->roles->first();

        return [
            $user->id,
            $user->name,
            $user->email,
            $role?->id,
            $role?->name ?? 'user',
            $user->project_id,
            optional($user->project)->name ?? 'None',
            $user->must_change_password ? 'Yes' : 'No',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
