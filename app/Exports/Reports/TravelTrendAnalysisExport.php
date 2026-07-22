<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TravelTrendAnalysisExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private array $currentData,
        private array $previousData,
        private string $period
    ) {
    }

    public function collection(): Collection
    {
        $rows = [];

        foreach (['total', 'approved', 'rejected', 'pending'] as $status) {
            $rows[] = (object) [
                'status' => ucfirst($status),
                'current' => $this->currentData[$status] ?? 0,
                'previous' => $this->previousData[$status] ?? 0,
                'change' => ($this->currentData[$status] ?? 0) - ($this->previousData[$status] ?? 0),
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Status',
            'Current Period',
            'Previous Period',
            'Change',
        ];
    }

    public function map($row): array
    {
        return [
            $row->status,
            $row->current,
            $row->previous,
            $row->change > 0 ? '+' . $row->change : $row->change,
        ];
    }
}
