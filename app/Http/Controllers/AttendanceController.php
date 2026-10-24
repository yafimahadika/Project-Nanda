<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Imports\AttendancesImport;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $sort = in_array($request->query('sort'), ['attendance_date', 'attendance_status', 'shift', 'created_at'], true)
            ? $request->query('sort')
            : 'attendance_date';

        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->query('search'));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $perPageOptions = ['5', '10', '20', '50', '100', 'all'];
        $perPage = in_array((string) $request->query('per_page'), $perPageOptions, true)
            ? (string) $request->query('per_page')
            : '5';

        $attendanceQuery = Attendance::query()
            ->with('employee')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('attendance_status', 'like', "%{$search}%")
                        ->orWhere('day', 'like', "%{$search}%")
                        ->orWhere('shift', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($query) use ($search) {
                            $query->where('employee_id', 'like', "%{$search}%")
                                ->orWhere('full_name', 'like', "%{$search}%")
                                ->orWhere('branch', 'like', "%{$search}%")
                                ->orWhere('job_position', 'like', "%{$search}%");
                        });
                });
            })
            ->when($dateFrom, fn ($query) => $query->whereDate('attendance_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('attendance_date', '<=', $dateTo))
            ->when($sort === 'attendance_date', fn ($query) => $query->orderBy('attendance_date', $direction))
            ->when($sort !== 'attendance_date', fn ($query) => $query->orderBy($sort, $direction));

        $paginationSize = $perPage === 'all'
            ? max((clone $attendanceQuery)->count(), 1)
            : (int) $perPage;

        $attendances = $attendanceQuery
            ->paginate($paginationSize)
            ->withQueryString();

        return view('attendances.index', [
            'attendances' => $attendances,
            'employees' => Employee::query()->orderBy('full_name')->get(),
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
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
    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $employee = Employee::where('employee_id', $request->validated('employee_code'))->firstOrFail();

        Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $request->validated('attendance_date'),
            ],
            [
                'day' => $request->validated('day'),
                'shift' => $request->validated('shift'),
                'attendance_status' => $request->validated('attendance_status'),
                'check_in' => $request->validated('check_in'),
                'check_out' => $request->validated('check_out'),
            ]
        );

        return redirect()
            ->route('attendances.index')
            ->with('alert', [
                'type' => 'success',
                'message' => 'Data absensi berhasil disimpan.',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        $employee = Employee::where('employee_id', $request->validated('employee_code'))->firstOrFail();

        $attendance->update([
            'employee_id' => $employee->id,
            'attendance_date' => $request->validated('attendance_date'),
            'day' => $request->validated('day'),
            'shift' => $request->validated('shift'),
            'attendance_status' => $request->validated('attendance_status'),
            'check_in' => $request->validated('check_in'),
            'check_out' => $request->validated('check_out'),
        ]);

        return redirect()
            ->route('attendances.index', $request->only(['search', 'sort', 'direction', 'date_from', 'date_to', 'per_page', 'page']))
            ->with('alert', [
                'type' => 'success',
                'message' => 'Data absensi berhasil diperbarui.',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return redirect()
            ->route('attendances.index', $request->only(['search', 'sort', 'direction', 'date_from', 'date_to', 'per_page', 'page']))
            ->with('alert', [
                'type' => 'success',
                'message' => 'Data absensi berhasil dihapus.',
            ]);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_ids' => ['required', 'array', 'min:1'],
            'attendance_ids.*' => ['integer', 'exists:attendances,id'],
        ], [
            'attendance_ids.required' => 'Pilih minimal satu data absensi yang ingin dihapus.',
            'attendance_ids.min' => 'Pilih minimal satu data absensi yang ingin dihapus.',
        ]);

        $deleted = Attendance::whereIn('id', $validated['attendance_ids'])->delete();

        return redirect()
            ->route('attendances.index', $request->only(['search', 'sort', 'direction', 'date_from', 'date_to', 'per_page', 'page']))
            ->with('alert', [
                'type' => 'success',
                'message' => "{$deleted} data absensi berhasil dihapus.",
            ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new AttendancesImport();

        Excel::import($import, $request->file('file'));

        $summary = $import->summary();

        return redirect()
            ->route('attendances.index')
            ->with('alert', [
                'type' => 'success',
                'message' => "Import selesai. Disimpan/diperbarui: {$summary['imported']}, dilewati karena Employee ID belum terdaftar: {$summary['skipped']}, gagal dibaca: {$summary['failed']}.",
            ]);
    }
}
