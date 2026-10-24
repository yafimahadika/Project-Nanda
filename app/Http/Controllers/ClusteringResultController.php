<?php

namespace App\Http\Controllers;

use App\Models\ClusterResult;
use App\Models\ClusteringRun;
use App\Services\EmployeeRankingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClusteringResultController extends Controller
{
    public function index(Request $request, EmployeeRankingService $rankingService): View
    {
        $runs = ClusteringRun::query()
            ->latest()
            ->limit(25)
            ->get();

        $selectedRun = $this->selectedRun($request, $runs);
        $search = trim((string) $request->query('search', ''));
        $cluster = $request->query('cluster', 'all');
        $perPage = $request->query('per_page', '10');

        $resultsQuery = ClusterResult::query()
            ->with('employee')
            ->when($selectedRun, fn ($query) => $query->where('clustering_run_id', $selectedRun->id))
            ->when(! $selectedRun, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($cluster !== 'all', fn ($query) => $query->where('cluster_number', (int) $cluster))
            ->when($search, function ($query) use ($search) {
                $query->whereHas('employee', function ($query) use ($search) {
                    $query->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('branch', 'like', "%{$search}%")
                        ->orWhere('job_position', 'like', "%{$search}%");
                });
            })
            ->join('employees', 'cluster_results.employee_id', '=', 'employees.id')
            ->select('cluster_results.*')
            ->orderBy('cluster_number')
            ->orderBy('employees.employee_id');

        $totalFiltered = (clone $resultsQuery)->count();
        $perPageValue = $perPage === 'all' ? max($totalFiltered, 1) : (int) $perPage;

        $results = $resultsQuery
            ->paginate($perPageValue)
            ->withQueryString();

        $summary = $this->summary($selectedRun);
        $rankings = $selectedRun ? $rankingService->rank($selectedRun) : collect();
        $rankByResultId = $rankings->keyBy(fn (array $row) => $row['result']->id);

        return view('clustering-results.index', [
            'runs' => $runs,
            'selectedRun' => $selectedRun,
            'results' => $results,
            'summary' => $summary,
            'search' => $search,
            'cluster' => $cluster,
            'perPage' => $perPage,
            'rankings' => $rankings,
            'rankByResultId' => $rankByResultId,
        ]);
    }

    private function selectedRun(Request $request, $runs): ?ClusteringRun
    {
        $runId = $request->query('run_id');

        $query = ClusteringRun::query()
            ->with(['centroids', 'results.employee']);

        if ($runId) {
            return $query->whereKey($runId)->first();
        }

        return $runs->isNotEmpty()
            ? $query->whereKey($runs->first()->id)->first()
            : null;
    }

    private function summary(?ClusteringRun $run): array
    {
        if (! $run) {
            return [
                'labels' => [],
                'counts' => [],
                'clusters' => collect(),
            ];
        }

        $counts = $run->results
            ->groupBy('cluster_number')
            ->map(fn ($items) => $items->count());

        $clusters = $run->centroids
            ->sortBy('cluster_number')
            ->map(function ($centroid) use ($counts) {
                return [
                    'number' => $centroid->cluster_number,
                    'label' => $centroid->cluster_label,
                    'count' => $counts->get($centroid->cluster_number, 0),
                    'values' => $centroid->values,
                ];
            })
            ->values();

        return [
            'labels' => $clusters->map(fn (array $cluster) => 'C' . $cluster['number'] . ' - ' . $cluster['label'])->toArray(),
            'counts' => $clusters->pluck('count')->toArray(),
            'clusters' => $clusters,
        ];
    }
}
