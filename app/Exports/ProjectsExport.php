<?php
namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Project::with('manager')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Project Code',
            'Description',
            'Location',
            'Region',
            'Discipline',
            'Status',
            'Start Date',
            'End Date',
            'Manager Name',
            'Created At'
        ];
    }

    public function map($project): array
    {
        return [
            $project->id,
            $project->name,
            $project->project_code,
            $project->description,
            $project->location,
            $project->region,
            $project->discipline,
            $project->status,
            $project->start_date?->format('Y-m-d') ?? '',
            $project->end_date?->format('Y-m-d') ?? '',
            optional($project->manager)->name ?? 'None',
            $project->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
