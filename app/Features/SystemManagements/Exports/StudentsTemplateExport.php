<?php

namespace App\Features\SystemManagements\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'John Doe',
                'john.doe@example.com',
                'password123',
                '+20',
                '1234567890',
                '2000-01-15',
                'male',
                'Grade 4',
                'Egyptian',
                '123 Main Street',
                'Cairo',
                'Cairo',
                'Egypt',
                '12345',
                'Jane Doe',
                '0987654321',
                'Student bio here',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'phone_code',
            'phone_number',
            'date_of_birth',
            'gender',
            'grade',
            'nationality',
            'address',
            'city',
            'state',
            'country',
            'postal_code',
            'emergency_contact_name',
            'emergency_contact_phone',
            'bio',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
            2 => [
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '808080'],
                ],
            ],
        ];
    }
}
