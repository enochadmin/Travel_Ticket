<?php
namespace App\Imports;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProjectsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $managerId = null;
        if (!empty($row['manager_email'])) {
            $manager = User::where('email', $row['manager_email'])->first();
            if ($manager) {
                $managerId = $manager->id;
                // Give role if not has it
                if (!$manager->hasRole('project-manager') && !$manager->hasAnyRole(['admin', 'ceo', 'head-office-director', 'commercial-director'])) {
                    $manager->assignRole('project-manager');
                }
            }
        }

        // Handle possible excel date serialization
        $startDate = !empty($row['start_date']) ? Carbon::parse($row['start_date'])->format('Y-m-d') : null;
        $endDate = !empty($row['end_date']) ? Carbon::parse($row['end_date'])->format('Y-m-d') : null;

        $project = Project::updateOrCreate(
            ['project_code' => $row['project_code'] ?? null],
            [
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'location' => $row['location'] ?? null,
                'region' => $row['region'] ?? null,
                'discipline' => $row['discipline'] ?? null,
                'status' => $row['status'] ?? 'active',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'manager_id' => $managerId,
            ]
        );

        if ($managerId) {
            User::whereKey($managerId)->update(['project_id' => $project->id]);
            $project->members()->syncWithoutDetaching([$managerId]);
        }

        return $project;
    }
}
