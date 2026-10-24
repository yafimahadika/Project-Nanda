<?php

namespace App\Http\Controllers;

use App\Models\ClusteringRun;
use App\Services\SilhouetteService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ChartController extends Controller
{
    public function index(Request $request, SilhouetteService $silhouetteService): View
    {
        $runs = ClusteringRun::query()
            ->latest()
            ->limit(25)
            ->get();

        $selectedRun = $this->selectedRun($request, $runs);
        $evaluation = $selectedRun ? $silhouetteService->evaluate($selectedRun) : null;
        $summary = $selectedRun ? $this->summary($selectedRun) : null;

        return view('charts.index', [
            'runs' => $runs,
            'selectedRun' => $selectedRun,
            'evaluation' => $evaluation,
            'summary' => $summary,
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

    private function summary(ClusteringRun $run): array
    {
        $clusters = $run->results
            ->groupBy('cluster_number')
            ->sortKeys()
            ->map(function (Collection $items, int $clusterNumber) {
                $first = $items->first();

                return [
                    'label' => 'C' . $clusterNumber . ' - ' . $first->cluster_label,
                    'count' => $items->count(),
                    'average_distance' => round($items->avg(fn ($item) => (float) $item->distance) ?? 0, 6),
                    'average_score' => round($items->avg(fn ($item) => collect($item->normalized_values ?? [])->avg()) ?? 0, 6),
                ];
            })
            ->values();

        return [
            'labels' => $clusters->pluck('label')->toArray(),
            'counts' => $clusters->pluck('count')->toArray(),
            'distances' => $clusters->pluck('average_distance')->toArray(),
            'scores' => $clusters->pluck('average_score')->toArray(),
            'clusters' => $clusters,
        ];
    }
}
