<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TravelRequestsReportExport implements FromCollection, WithHeadings, WithMapping
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
            'ID',
            'Requester',
            'Project',
            'Origin',
            'Destination',
            'Travel Date',
            'Return Date',
            'Passengers',
            'Flight Type',
            'Status',
            'Purpose',
            'PM Approver',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->user?->name,
            $row->project?->name,
            $row->origin,
            $row->destination,
            $row->travel_date,
            $row->return_date,
            $row->passenger_count,
            $row->flight_type,
            ucfirst(str_replace('_', ' ', $row->status)),
            $row->purpose,
            $row->pm?->name,
        ];
    }
}
