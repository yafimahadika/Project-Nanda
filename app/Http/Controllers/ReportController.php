<?php

namespace App\Http\Controllers;

use App\Models\ClusteringRun;
use App\Services\EmployeeRankingService;
use App\Services\SilhouetteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(
        Request $request,
        SilhouetteService $silhouetteService,
        EmployeeRankingService $rankingService
    ): View
    {
        $runs = ClusteringRun::query()
            ->latest()
            ->limit(25)
            ->get();

        $selectedRun = $this->selectedRun($request, $runs);
        $evaluation = $selectedRun
            ? $silhouetteService->evaluate($selectedRun)
            : null;
        $rankings = $selectedRun ? $rankingService->rank($selectedRun) : collect();

        return view('reports.index', [
            'runs' => $runs,
            'selectedRun' => $selectedRun,
            'evaluation' => $evaluation,
            'rankings' => $rankings,
        ]);
    }

    private function selectedRun(Request $request, $runs): ?ClusteringRun
    {
        $query = ClusteringRun::query()
            ->with(['centroids', 'results.employee']);

        if ($request->query('run_id')) {
            return $query->whereKey($request->query('run_id'))->first();
        }

        return $runs->isNotEmpty()
            ? $query->whereKey($runs->first()->id)->first()
            : null;
    }
}
