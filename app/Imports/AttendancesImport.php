<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AttendancesImport implements ToCollection, WithHeadingRow
{
    private int $imported = 0;

    private int $skipped = 0;

    private int $failed = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $employeeCode = trim((string) ($row['employee_id'] ?? ''));

            if ($employeeCode === '') {
                $this->failed++;
                continue;
            }

            $employee = Employee::where('employee_id', $employeeCode)->first();

            if (! $employee) {
                $this->skipped++;
                continue;
            }

            $attendanceDate = $this->parseDate($row['date'] ?? null);
            $attendanceStatus = trim((string) ($row['attendance'] ?? ''));

            if (! $attendanceDate || $attendanceStatus === '') {
                $this->failed++;
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'attendance_date' => $attendanceDate,
                ],
                [
                    'day' => $this->nullableString($row['day'] ?? null),
                    'shift' => $this->nullableString($row['shift'] ?? null),
                    'attendance_status' => $attendanceStatus,
                    'check_in' => $this->parseTime($row['check_in'] ?? null),
                    'check_out' => $this->parseTime($row['check_out'] ?? null),
                ]
            );

            $this->imported++;
        }
    }

    public function summary(): array
    {
        return [
            'imported' => $this->imported,
            'skipped' => $this->skipped,
            'failed' => $this->failed,
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('H:i:s');
        }

        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
