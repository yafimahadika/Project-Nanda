<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_code' => ['required', 'string', Rule::exists('employees', 'employee_id')],
            'attendance_date' => ['required', 'date'],
            'day' => ['nullable', 'string', 'max:30'],
            'shift' => ['nullable', 'string', 'max:50'],
            'attendance_status' => ['required', 'string', 'max:50'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $employee = Employee::where('employee_id', $this->input('employee_code'))->first();
            $attendance = $this->route('attendance');

            if (! $employee || ! $attendance || ! $this->input('attendance_date')) {
                return;
            }

            $alreadyExists = Attendance::where('employee_id', $employee->id)
                ->whereDate('attendance_date', $this->input('attendance_date'))
                ->whereKeyNot($attendance->id)
                ->exists();

            if ($alreadyExists) {
                $validator->errors()->add(
                    'attendance_date',
                    'Data absensi untuk Employee ID dan tanggal tersebut sudah ada.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'employee_code' => 'employee ID',
            'attendance_date' => 'tanggal absensi',
            'attendance_status' => 'status absensi',
            'check_in' => 'check in',
            'check_out' => 'check out',
        ];
    }
}
