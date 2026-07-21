<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MostTraveledCitiesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private Collection $rows,
        private string $cityField = 'destination'
    ) {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Rank',
            'City',
            'Region',
            'Request Count',
            'Passenger Count',
            'View Type',
        ];
    }

    public function map($row): array
    {
        return [
            $row->rank,
            $row->city,
            $row->region,
            $row->request_count,
            $row->passenger_count,
            $this->cityFieldLabel(),
        ];
    }

    private function cityFieldLabel(): string
    {
        return match ($this->cityField) {
            'origin' => 'Origin',
            'all' => 'Origin + Destination',
            default => 'Destination',
        };
    }
}
