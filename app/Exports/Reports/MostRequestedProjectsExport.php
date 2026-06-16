<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MostRequestedProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Project',
            'Project Code',
            'Region',
            'Head Office',
            'Request Count',
            'Passenger Count',
            'Approved',
            'Pending',
            'Rejected',
        ];
    }

    public function map($row): array
    {
        return [
            $row->rank,
            $row->project_name,
            $row->project_code,
            $row->region,
            $row->is_head_office ? 'Yes' : 'No',
            $row->request_count,
            $row->passenger_count,
            $row->approved_count,
            $row->pending_count,
            $row->rejected_count,
        ];
    }
}
