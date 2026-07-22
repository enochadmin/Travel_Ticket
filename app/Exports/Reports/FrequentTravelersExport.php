<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FrequentTravelersExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $travelers)
    {
    }

    public function collection(): Collection
    {
        return $this->travelers;
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Traveler Name',
            'Email',
            'Total Trips',
            'Destinations',
            'Projects',
        ];
    }

    public function map($row): array
    {
        static $rank = 0;
        $rank++;

        return [
            $rank,
            $row->user_name,
            $row->user_email,
            $row->trip_count,
            $row->destinations,
            $row->projects,
        ];
    }
}
