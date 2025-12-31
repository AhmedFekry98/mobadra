<?php

namespace App\Features\SystemManagements\Exports;

use App\Features\SystemManagements\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::with(['userInformation.grade', 'role'])
            ->whereHas('role', function ($q) {
                $q->where('name', 'student');
            });

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['grade_id'])) {
            $query->whereHas('userInformation', function ($q) {
                $q->where('grade_id', $this->filters['grade_id']);
            });
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone Code',
            'Phone Number',
            'Date of Birth',
            'Gender',
            'Grade',
            'Nationality',
            'Address',
            'City',
            'State',
            'Country',
            'Postal Code',
            'Emergency Contact Name',
            'Emergency Contact Phone',
            'Bio',
            'Email Verified',
            'Phone Verified',
            'Created At',
        ];
    }

    public function map($student): array
    {
        $info = $student->userInformation;

        return [
            $student->id,
            $student->name,
            $student->email,
            $info?->phone_code,
            $info?->phone_number,
            $info?->date_of_birth?->format('Y-m-d'),
            $info?->gender?->value ?? $info?->gender,
            $info?->grade?->name,
            $info?->nationality,
            $info?->address,
            $info?->city,
            $info?->state,
            $info?->country,
            $info?->postal_code,
            $info?->emergency_contact_name,
            $info?->emergency_contact_phone,
            $info?->bio,
            $student->email_verified_at ? 'Yes' : 'No',
            $student->phone_verified_at ? 'Yes' : 'No',
            $student->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
    }
}
