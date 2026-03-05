<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProjectsTemplateExport implements FromArray, WithHeadings, WithEvents
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Project Code',
            'Description',
            'Location',
            'Region',
            'Discipline',
            'Status',
            'Start Date',
            'End Date',
            'Manager Email'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Configure array of disciplines and statuses
                $disciplines = '"Infrastructure,Water,Building"';
                $statuses = '"active,on-hold,completed,cancelled"';

                // Discipline validation on Column F
                $valDiscipline = $sheet->getCell('F2')->getDataValidation();
                $valDiscipline->setType(DataValidation::TYPE_LIST);
                $valDiscipline->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $valDiscipline->setAllowBlank(true);
                $valDiscipline->setShowInputMessage(true);
                $valDiscipline->setShowErrorMessage(true);
                $valDiscipline->setShowDropDown(true);
                $valDiscipline->setErrorTitle('Input error');
                $valDiscipline->setError('Value is not within the acceptable list.');
                $valDiscipline->setPromptTitle('Pick Discipline');
                $valDiscipline->setFormula1($disciplines);

                // Status validation on Column G
                $valStatus = $sheet->getCell('G2')->getDataValidation();
                $valStatus->setType(DataValidation::TYPE_LIST);
                $valStatus->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $valStatus->setAllowBlank(true);
                $valStatus->setShowInputMessage(true);
                $valStatus->setShowErrorMessage(true);
                $valStatus->setShowDropDown(true);
                $valStatus->setErrorTitle('Input error');
                $valStatus->setError('Value is not within the acceptable list.');
                $valStatus->setPromptTitle('Pick Status');
                $valStatus->setFormula1($statuses);

                for ($i = 3; $i <= 1000; $i++) {
                    $sheet->getCell("F{$i}")->setDataValidation(clone $valDiscipline);
                    $sheet->getCell("G{$i}")->setDataValidation(clone $valStatus);
                }
            },
        ];
    }
}
