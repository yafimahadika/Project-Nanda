<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
        /** @var Employee|null $employee */
        $employee = $this->route('employee');

        return [
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_id')->ignore($employee?->id),
            ],
            'full_name' => ['required', 'string', 'max:150'],
            'branch' => ['nullable', 'string', 'max:100'],
            'job_position' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_id' => 'employee ID',
            'full_name' => 'nama lengkap',
            'branch' => 'cabang',
            'job_position' => 'posisi pekerjaan',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'Employee ID sudah dipakai. Gunakan Employee ID yang berbeda.',
        ];
    }
}
