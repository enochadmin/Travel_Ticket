<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Spatie\Permission\Models\Role;

class UsersTemplateExport implements FromArray, WithHeadings, WithEvents, WithTitle
{
    public function array(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Users';
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'project_id',
            'role_id',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $spreadsheet = $event->sheet->getDelegate()->getParent();
                $usersSheet = $event->sheet->getDelegate();

                $rolesSheet = $spreadsheet->createSheet();
                $rolesSheet->setTitle('Roles');
                $rolesSheet->setCellValue('A1', 'role_id');
                $rolesSheet->setCellValue('B1', 'role_name');

                $roleRow = 2;
                foreach (Role::orderBy('id')->get() as $role) {
                    $rolesSheet->setCellValue("A{$roleRow}", $role->id);
                    $rolesSheet->setCellValue("B{$roleRow}", $role->name);
                    $roleRow++;
                }
                $lastRoleRow = max(2, $roleRow - 1);

                $projectsSheet = $spreadsheet->createSheet();
                $projectsSheet->setTitle('Projects');
                $projectsSheet->setCellValue('A1', 'project_id');
                $projectsSheet->setCellValue('B1', 'project_name');

                $projectRow = 2;
                foreach (Project::orderBy('name')->get() as $project) {
                    $projectsSheet->setCellValue("A{$projectRow}", $project->id);
                    $projectsSheet->setCellValue("B{$projectRow}", $project->name);
                    $projectRow++;
                }
                $lastProjectRow = max(2, $projectRow - 1);

                $spreadsheet->setActiveSheetIndex(0);

                $usersSheet->setCellValue('A1', 'name');
                $usersSheet->setCellValue('B1', 'email');
                $usersSheet->setCellValue('C1', 'project_id');
                $usersSheet->setCellValue('D1', 'role_id');
                $usersSheet->getColumnDimension('A')->setWidth(28);
                $usersSheet->getColumnDimension('B')->setWidth(32);
                $usersSheet->getColumnDimension('C')->setWidth(14);
                $usersSheet->getColumnDimension('D')->setWidth(12);

                $roleValidation = $usersSheet->getCell('D2')->getDataValidation();
                $roleValidation->setType(DataValidation::TYPE_LIST);
                $roleValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $roleValidation->setAllowBlank(false);
                $roleValidation->setShowInputMessage(true);
                $roleValidation->setShowErrorMessage(true);
                $roleValidation->setShowDropDown(true);
                $roleValidation->setErrorTitle('Invalid role');
                $roleValidation->setError('Select a role ID from the Roles sheet.');
                $roleValidation->setPromptTitle('Role ID');
                $roleValidation->setPrompt('Pick a role ID from the dropdown (see Roles sheet for names).');
                $roleValidation->setFormula1("=Roles!\$A\$2:\$A\${$lastRoleRow}");

                $projectValidation = $usersSheet->getCell('C2')->getDataValidation();
                $projectValidation->setType(DataValidation::TYPE_LIST);
                $projectValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $projectValidation->setAllowBlank(true);
                $projectValidation->setShowInputMessage(true);
                $projectValidation->setShowErrorMessage(true);
                $projectValidation->setShowDropDown(true);
                $projectValidation->setErrorTitle('Invalid project');
                $projectValidation->setError('Select a project ID from the Projects sheet or leave blank.');
                $projectValidation->setPromptTitle('Project ID');
                $projectValidation->setPrompt('Optional. Pick a project ID from the dropdown.');
                $projectValidation->setFormula1("=Projects!\$A\$2:\$A\${$lastProjectRow}");

                for ($i = 2; $i <= 1000; $i++) {
                    $usersSheet->getCell("D{$i}")->setDataValidation(clone $roleValidation);
                    if ($lastProjectRow >= 2) {
                        $usersSheet->getCell("C{$i}")->setDataValidation(clone $projectValidation);
                    }
                }
            },
        ];
    }
}
