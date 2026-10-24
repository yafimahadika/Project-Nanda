<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClusteringRun;
use App\Models\Employee;
use App\Services\EmployeeRankingService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(EmployeeRankingService $rankingService): View
    {
        $latestRun = ClusteringRun::query()
            ->with('results.employee')
            ->latest()
            ->first();

        $clusterSummary = [
            'Kinerja Tinggi' => 0,
            'Kinerja Sedang' => 0,
            'Kinerja Rendah' => 0,
        ];

        if ($latestRun) {
            foreach ($latestRun->results as $result) {
                if ($result->cluster_label && array_key_exists($result->cluster_label, $clusterSummary)) {
                    $clusterSummary[$result->cluster_label]++;
                }
            }
        }

        $topEmployees = $latestRun
            ? $rankingService->rank($latestRun)->take(5)
            : collect();

        $attendanceStatusSummary = Attendance::query()
            ->select('attendance_status', DB::raw('COUNT(*) as total'))
            ->whereNotNull('attendance_status')
            ->groupBy('attendance_status')
            ->orderBy('attendance_status')
            ->pluck('total', 'attendance_status')
            ->toArray();

        return view('dashboard', [
            'totalEmployees' => Employee::count(),
            'totalAttendances' => Attendance::count(),
            'totalClusteringRuns' => ClusteringRun::count(),
            'latestRun' => $latestRun,
            'clusterSummary' => $clusterSummary,
            'attendanceStatusSummary' => $attendanceStatusSummary,
            'topEmployees' => $topEmployees,
        ]);
    }
}
