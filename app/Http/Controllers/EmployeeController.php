<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $sort = in_array($request->query('sort'), ['employee_id', 'full_name', 'branch', 'job_position', 'created_at'], true)
            ? $request->query('sort')
            : 'employee_id';

        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $search = trim((string) $request->query('search'));

        $employees = Employee::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('branch', 'like', "%{$search}%")
                        ->orWhere('job_position', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(5)
            ->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function checkEmployeeId(Request $request): JsonResponse
    {
        $employeeId = trim((string) $request->query('employee_id'));
        $ignoreId = $request->query('ignore_id');

        $exists = $employeeId !== '' && Employee::query()
            ->where('employee_id', $employeeId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Employee ID sudah dipakai. Gunakan Employee ID yang berbeda.' : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Employee::create($request->validated());

        return redirect()
            ->route('employees.index')
            ->with('alert', [
                'type' => 'success',
                'message' => 'Data karyawan berhasil ditambahkan.',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()
            ->route('employees.index', $request->only(['search', 'sort', 'direction', 'page']))
            ->with('alert', [
                'type' => 'success',
                'message' => 'Data karyawan berhasil diperbarui.',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()
            ->route('employees.index', $request->only(['search', 'sort', 'direction', 'page']))
            ->with('alert', [
                'type' => 'success',
                'message' => 'Data karyawan berhasil dihapus.',
            ]);
    }
}
