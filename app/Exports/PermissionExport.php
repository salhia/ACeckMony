<?php

namespace App\Exports;

use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PermissionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Retrieve the data
        $permissions = Permission::select('name', 'group_name')->get();

        // Loop through each permission and replace null/empty values with 'N/A'
        $permissions->transform(function ($permission) {
            return [
                'name' => $permission->name ?: 'N/A', // Replace null/empty 'name' with 'N/A'
                'group_name' => $permission->group_name ?: 'N/A', // Replace null/empty 'group_name' with 'N/A'
            ];
        });

        return $permissions;
    }

    // This method provides the headers
    public function headings(): array
    {
        return [
            'Name',
            'Group Name'
        ];
    }

    // Apply styles to the header
    public function styles(Worksheet $sheet)
    {
        // Apply styles to the first row (header)
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'], // White text color
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '032063'], // Black background color
                ]
            ]
        ];
    }
}
