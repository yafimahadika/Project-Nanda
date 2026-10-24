<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
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
