<?php
namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name']) || empty($row['email'])) {
            return null;
        }

        $user = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['name'],
                'password' => !empty($row['password']) ? Hash::make($row['password']) : Hash::make('password123'),
            ]
        );

        if (!empty($row['role'])) {
            $user->syncRoles([$row['role']]);
        }

        return $user;
    }
}
