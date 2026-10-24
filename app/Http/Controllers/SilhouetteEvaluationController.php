<?php

namespace App\Http\Controllers;

use App\Models\ClusteringRun;
use App\Services\SilhouetteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SilhouetteEvaluationController extends Controller
{
    public function index(Request $request, SilhouetteService $silhouetteService): View
    {
        $runs = ClusteringRun::query()
            ->latest()
            ->limit(25)
            ->get();

        $selectedRun = $this->selectedRun($request, $runs);
        $evaluation = $selectedRun
            ? $silhouetteService->evaluate($selectedRun)
            : null;

        return view('silhouette.index', [
            'runs' => $runs,
            'selectedRun' => $selectedRun,
            'evaluation' => $evaluation,
        ]);
    }

    private function selectedRun(Request $request, $runs): ?ClusteringRun
    {
        $query = ClusteringRun::query()
            ->with(['results.employee']);

        if ($request->query('run_id')) {
            return $query->whereKey($request->query('run_id'))->first();
        }

        return $runs->isNotEmpty()
            ? $query->whereKey($runs->first()->id)->first()
            : null;
    }
}
