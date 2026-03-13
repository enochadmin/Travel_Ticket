<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class UsersTemplateExport implements FromArray, WithHeadings, WithEvents
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Password',
            'Role'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Roles list
                $roles = ['admin', 'user', 'head-office-director', 'commercial-director', 'project-manager', 'ceo', 'reception'];
                $options = '"' . implode(',', $roles) . '"';

                // Apply validation to the 'Role' column (Column D) for rows 2 to 1000
                $validation = $event->sheet->getCell('D2')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a role from the drop-down list.');
                $validation->setFormula1($options);

                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("D{$i}")->setDataValidation(clone $validation);
                }
            },
        ];
    }
}
