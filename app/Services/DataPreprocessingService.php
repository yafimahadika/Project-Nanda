<?php

namespace App\Services;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DataPreprocessingService
{
    public function summarize(?string $dateFrom = null, ?string $dateTo = null, ?string $search = null): Collection
    {
        return Employee::query()
            ->with(['attendances' => function ($query) use ($dateFrom, $dateTo) {
                $query
                    ->when($dateFrom, fn ($query) => $query->whereDate('attendance_date', '>=', $dateFrom))
                    ->when($dateTo, fn ($query) => $query->whereDate('attendance_date', '<=', $dateTo))
                    ->orderBy('attendance_date');
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('branch', 'like', "%{$search}%")
                        ->orWhere('job_position', 'like', "%{$search}%");
                });
            })
            ->orderBy('employee_id')
            ->get()
            ->map(fn (Employee $employee) => $this->summarizeEmployee($employee))
            ->filter(fn (array $row) => $row['total_records'] > 0)
            ->values();
    }

    private function summarizeEmployee(Employee $employee): array
    {
        $totalRecords = $employee->attendances->count();
        $presentCount = 0;
        $notPresentCount = 0;
        $permissionCount = 0;
        $sickCount = 0;
        $leaveCount = 0;
        $alphaCount = 0;
        $lateCount = 0;
        $totalLateMinutes = 0;

        foreach ($employee->attendances as $attendance) {
            $status = $this->normalizeStatus($attendance->attendance_status);

            if ($this->isPresent($attendance->attendance_status)) {
                $presentCount++;
            } else {
                $notPresentCount++;
            }

            if ($this->isPermission($status)) {
                $permissionCount++;
            }

            if ($this->isSick($status)) {
                $sickCount++;
            }

            if ($this->isLeave($status)) {
                $leaveCount++;
            }

            if ($this->isAlpha($status)) {
                $alphaCount++;
            }

            $lateMinutes = $this->lateMinutes($attendance->shift, $attendance->check_in);

            if ($lateMinutes > 0) {
                $lateCount++;
                $totalLateMinutes += $lateMinutes;
            }
        }

        return [
            'employee_id' => $employee->employee_id,
            'full_name' => $employee->full_name,
            'branch' => $employee->branch,
            'job_position' => $employee->job_position,
            'total_records' => $totalRecords,
            'present_count' => $presentCount,
            'not_present_count' => $notPresentCount,
            'permission_count' => $permissionCount,
            'sick_count' => $sickCount,
            'leave_count' => $leaveCount,
            'alpha_count' => $alphaCount,
            'attendance_percentage' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0,
            'late_count' => $lateCount,
            'total_late_minutes' => $totalLateMinutes,
        ];
    }

    private function normalizeStatus(?string $status): string
    {
        return strtolower(trim((string) $status));
    }

    private function isPresent(?string $status): bool
    {
        $status = $this->normalizeStatus($status);

        return str_contains($status, 'hadir')
            || str_contains($status, 'present')
            || str_contains($status, 'masuk')
            || str_contains($status, 'telat')
            || str_contains($status, 'terlambat')
            || str_contains($status, 'late');
    }

    private function isPermission(string $status): bool
    {
        return str_contains($status, 'izin')
            || str_contains($status, 'ijin')
            || str_contains($status, 'permission');
    }

    private function isSick(string $status): bool
    {
        return str_contains($status, 'sakit')
            || str_contains($status, 'sick');
    }

    private function isLeave(string $status): bool
    {
        return str_contains($status, 'cuti')
            || str_contains($status, 'leave');
    }

    private function isAlpha(string $status): bool
    {
        return str_contains($status, 'alpha')
            || str_contains($status, 'alpa')
            || str_contains($status, 'absent');
    }

    private function lateMinutes(?string $shift, ?string $checkIn): int
    {
        if (! $shift || ! $checkIn) {
            return 0;
        }

        if (! preg_match('/(\d{1,2}:\d{2})\s*[-–]/', $shift, $matches)) {
            return 0;
        }

        try {
            $shiftStart = Carbon::createFromFormat('H:i', $matches[1]);
            $actualCheckIn = Carbon::parse($checkIn);
        } catch (\Throwable) {
            return 0;
        }

        if ($actualCheckIn->lessThanOrEqualTo($shiftStart)) {
            return 0;
        }

        return $shiftStart->diffInMinutes($actualCheckIn);
    }
}
