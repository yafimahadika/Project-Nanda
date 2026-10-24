<?php

namespace App\Services;

use App\Models\ClusterCentroid;
use App\Models\ClusterResult;
use App\Models\ClusteringRun;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KMeansService
{
    public const K = 3;

    public const MAX_ITERATIONS = 100;

    public function run(Collection $normalizationRows, array $attributes, ?string $period = null): ClusteringRun
    {
        $points = $normalizationRows->map(function (array $row) {
            return [
                'employee_id' => $row['employee_id'],
                'full_name' => $row['full_name'],
                'original' => $row['original'],
                'normalized' => $row['normalized'],
                'vector' => array_values($row['normalized']),
            ];
        })->values();

        if ($points->count() < self::K) {
            throw new \InvalidArgumentException('Minimal data untuk K-Means adalah 3 karyawan.');
        }

        $centroids = $this->initialCentroids($points);
        $previousAssignments = [];
        $history = [];
        $assignments = [];
        $distancesByPoint = [];

        for ($iteration = 1; $iteration <= self::MAX_ITERATIONS; $iteration++) {
            [$assignments, $distancesByPoint] = $this->assignClusters($points, $centroids);
            $newCentroids = $this->recalculateCentroids($points, $assignments, $centroids);

            $history[] = [
                'iteration' => $iteration,
                'points' => $points
                    ->map(fn (array $point) => [
                        'employee_id' => $point['employee_id'],
                        'full_name' => $point['full_name'],
                    ])
                    ->values()
                    ->toArray(),
                'centroids' => $centroids,
                'distances' => $distancesByPoint,
                'assignments' => $assignments,
                'new_centroids' => $newCentroids,
            ];

            if ($assignments === $previousAssignments || $this->centroidsStable($centroids, $newCentroids)) {
                $centroids = $newCentroids;
                break;
            }

            $previousAssignments = $assignments;
            $centroids = $newCentroids;
        }

        $labels = $this->labelClusters($centroids);

        return DB::transaction(function () use ($period, $points, $attributes, $history, $centroids, $assignments, $distancesByPoint, $labels) {
            $run = ClusteringRun::create([
                'period' => $period,
                'total_data' => $points->count(),
                'total_clusters' => self::K,
                'iterations' => count($history),
                'attributes' => $attributes,
                'iteration_history' => $history,
            ]);

            foreach ($centroids as $clusterNumber => $values) {
                ClusterCentroid::create([
                    'clustering_run_id' => $run->id,
                    'cluster_number' => $clusterNumber + 1,
                    'cluster_label' => $labels[$clusterNumber],
                    'values' => $values,
                ]);
            }

            foreach ($points as $index => $point) {
                $employee = Employee::where('employee_id', $point['employee_id'])->firstOrFail();
                $clusterIndex = $assignments[$index];

                ClusterResult::create([
                    'clustering_run_id' => $run->id,
                    'employee_id' => $employee->id,
                    'cluster_number' => $clusterIndex + 1,
                    'cluster_label' => $labels[$clusterIndex],
                    'distance' => $distancesByPoint[$index][$clusterIndex],
                    'attribute_values' => $point['original'],
                    'normalized_values' => $point['normalized'],
                ]);
            }

            return $run->load(['centroids', 'results.employee']);
        });
    }

    private function initialCentroids(Collection $points): array
    {
        $sorted = $points->sortBy(fn (array $point) => array_sum($point['vector']))->values();
        $lastIndex = $sorted->count() - 1;

        return [
            $sorted->first()['vector'],
            $sorted[(int) floor($lastIndex / 2)]['vector'],
            $sorted->last()['vector'],
        ];
    }

    private function assignClusters(Collection $points, array $centroids): array
    {
        $assignments = [];
        $distancesByPoint = [];

        foreach ($points as $pointIndex => $point) {
            $distances = [];

            foreach ($centroids as $centroidIndex => $centroid) {
                $distances[$centroidIndex] = $this->euclideanDistance($point['vector'], $centroid);
            }

            $assignments[$pointIndex] = array_keys($distances, min($distances), true)[0];
            $distancesByPoint[$pointIndex] = $distances;
        }

        return [$assignments, $distancesByPoint];
    }

    private function recalculateCentroids(Collection $points, array $assignments, array $currentCentroids): array
    {
        $newCentroids = [];
        $dimension = count($currentCentroids[0]);

        for ($cluster = 0; $cluster < self::K; $cluster++) {
            $members = $points->filter(fn (array $point, int $index) => $assignments[$index] === $cluster);

            if ($members->isEmpty()) {
                $newCentroids[$cluster] = $currentCentroids[$cluster];
                continue;
            }

            $centroid = array_fill(0, $dimension, 0);

            foreach ($members as $member) {
                foreach ($member['vector'] as $index => $value) {
                    $centroid[$index] += $value;
                }
            }

            $newCentroids[$cluster] = array_map(
                fn (float $value) => round($value / $members->count(), 6),
                $centroid
            );
        }

        return $newCentroids;
    }

    private function euclideanDistance(array $point, array $centroid): float
    {
        $sum = 0;

        foreach ($point as $index => $value) {
            $sum += ($value - $centroid[$index]) ** 2;
        }

        return round(sqrt($sum), 6);
    }

    private function centroidsStable(array $oldCentroids, array $newCentroids): bool
    {
        foreach ($oldCentroids as $cluster => $centroid) {
            foreach ($centroid as $index => $value) {
                if (abs($value - $newCentroids[$cluster][$index]) > 0.000001) {
                    return false;
                }
            }
        }

        return true;
    }

    private function labelClusters(array $centroids): array
    {
        $scores = [];

        foreach ($centroids as $index => $centroid) {
            $scores[$index] = array_sum($centroid) / count($centroid);
        }

        arsort($scores);

        $orderedClusters = array_keys($scores);
        $labels = [];
        $labels[$orderedClusters[0]] = 'Kinerja Tinggi';
        $labels[$orderedClusters[1]] = 'Kinerja Sedang';
        $labels[$orderedClusters[2]] = 'Kinerja Rendah';

        return $labels;
    }
}
