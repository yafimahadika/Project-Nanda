<?php

namespace App\Http\Controllers;

use App\Services\DataPreprocessingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreprocessingController extends Controller
{
    public function __invoke(Request $request, DataPreprocessingService $preprocessingService): View
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = trim((string) $request->query('search'));

        $rows = $preprocessingService->summarize($dateFrom, $dateTo, $search ?: null);

        return view('preprocessing.index', [
            'rows' => $rows,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
            'summary' => [
                'employees' => $rows->count(),
                'records' => $rows->sum('total_records'),
                'average_attendance' => $rows->count() > 0 ? round($rows->avg('attendance_percentage'), 2) : 0,
                'late_minutes' => $rows->sum('total_late_minutes'),
            ],
        ]);
    }
}
