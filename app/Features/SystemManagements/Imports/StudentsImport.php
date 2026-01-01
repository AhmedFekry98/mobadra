<?php

namespace App\Features\SystemManagements\Imports;

use App\Features\Grades\Models\Grade;
use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Models\UserInformation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected array $errors = [];
    protected int $successCount = 0;
    protected int $failedCount = 0;
    protected array $failedRows = [];

    public function collection(Collection $rows)
    {
        $studentRole = Role::where('name', 'student')->first();

        if (!$studentRole) {
            $this->errors[] = 'Student role not found in the system.';
            return;
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header row

            try {
                $data = $this->validateRow($row->toArray(), $rowNumber);

                if ($data === false) {
                    continue;
                }

                DB::beginTransaction();

                // Check if user already exists by email
                $existingUser = User::where('email', $data['email'])->first();

                if ($existingUser) {
                    // Update existing user
                    $existingUser->update([
                        'name' => $data['name'],
                    ]);
                    $user = $existingUser;
                } else {
                    // Create new user with default password if not provided
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password'] ?? 'password'),
                        'role_id' => $studentRole->id,
                    ]);
                }

                // Find grade by name if provided
                $gradeId = null;
                if (!empty($data['grade'])) {
                    $grade = Grade::where('name', 'like', '%' . $data['grade'] . '%')->first();
                    $gradeId = $grade?->id;
                }

                // Update or create user information
                UserInformation::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'grade_id' => $gradeId,
                        'phone_code' => $data['phone_code'] ?? null,
                        'phone_number' => $data['phone_number'] ?? null,
                        'date_of_birth' => $data['date_of_birth'] ?? null,
                        'gender' => $data['gender'] ?? null,
                        'nationality' => $data['nationality'] ?? null,
                        'address' => $data['address'] ?? null,
                        'city' => $data['city'] ?? null,
                        'state' => $data['state'] ?? null,
                        'country' => $data['country'] ?? null,
                        'postal_code' => $data['postal_code'] ?? null,
                        'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                        'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                        'bio' => $data['bio'] ?? null,
                    ]
                );

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'error' => $e->getMessage(),
                ];
            }
        }
    }

    protected function validateRow(array $row, int $rowNumber): array|false
    {
        // Normalize keys to handle different header formats
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(str_replace([' ', '-'], '_', trim($key ?? '')));
            // Convert numeric values to strings for phone/postal fields
            $normalizedRow[$normalizedKey] = is_numeric($value) ? (string) $value : $value;
        }

        // Convert Excel date serial number to date string
        if (!empty($normalizedRow['date_of_birth'])) {
            $normalizedRow['date_of_birth'] = $this->parseDate($normalizedRow['date_of_birth']);
        }

        $validator = Validator::make($normalizedRow, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone_code' => 'nullable|max:10',
            'phone_number' => 'nullable|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,Male,Female,MALE,FEMALE',
            'grade' => 'nullable|max:255',
            'nationality' => 'nullable|max:100',
            'address' => 'nullable|max:500',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'postal_code' => 'nullable|max:20',
            'emergency_contact_name' => 'nullable|max:255',
            'emergency_contact_phone' => 'nullable|max:20',
            'bio' => 'nullable|max:1000',
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            $this->failedCount++;
            $this->failedRows[] = [
                'row' => $rowNumber,
                'data' => $normalizedRow,
                'error' => $validator->errors()->toArray(),
            ];
            return false;
        }

        $data = $validator->validated();

        // Normalize gender
        if (!empty($data['gender'])) {
            $data['gender'] = strtolower($data['gender']);
        }

        return $data;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportSummary(): array
    {
        return [
            'success_count' => $this->successCount,
            'failed_count' => $this->failedCount,
            'failed_rows' => $this->failedRows,
            'errors' => $this->errors,
        ];
    }

    /**
     * Parse date from various formats including Excel serial numbers
     */
    protected function parseDate(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // If it's a pure numeric value, it's likely an Excel date serial number
        if (is_numeric($value) && (int)$value > 1000 && (int)$value < 100000) {
            try {
                $date = ExcelDate::excelToDateTimeObject((int)$value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Fall through to other parsing methods
            }
        }

        // Try to parse various date formats
        $formats = [
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'm/d/Y',
            'Y/m/d',
            'd-m-Y :H:i',
            'd-m-Y:H:i',
            'Y-m-d H:i:s',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, trim($value));
                if ($date) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Try Carbon's flexible parsing as last resort
        try {
            $date = Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
