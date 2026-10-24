<?php

namespace App\Services;

use App\Models\ClusteringRun;
use Illuminate\Support\Collection;

class SilhouetteService
{
    public function evaluate(ClusteringRun $run): array
    {
        $results = $run->results->values();

        if ($results->count() < 2) {
            return [
                'average' => 0,
                'interpretation' => $this->interpretation(0),
                'rows' => collect(),
                'clusters' => collect(),
            ];
        }

        $points = $results->map(fn ($result) => [
            'id' => $result->id,
            'cluster' => (int) $result->cluster_number,
            'vector' => array_values($result->normalized_values ?? []),
            'result' => $result,
        ]);

        $rows = $points->map(function (array $point) use ($points) {
            $sameCluster = $points->filter(fn (array $candidate) => $candidate['cluster'] === $point['cluster'] && $candidate['id'] !== $point['id']);
            $otherClusters = $points->filter(fn (array $candidate) => $candidate['cluster'] !== $point['cluster'])->groupBy('cluster');

            $a = $sameCluster->isEmpty()
                ? 0
                : $sameCluster->avg(fn (array $candidate) => $this->distance($point['vector'], $candidate['vector']));

            $b = $otherClusters->isEmpty()
                ? 0
                : $otherClusters
                    ->map(fn (Collection $clusterPoints) => $clusterPoints->avg(fn (array $candidate) => $this->distance($point['vector'], $candidate['vector'])))
                    ->min();

            $max = max($a, $b);
            $score = $sameCluster->isEmpty() || $max <= 0 ? 0 : ($b - $a) / $max;

            return [
                'result' => $point['result'],
                'a' => round($a, 6),
                'b' => round($b, 6),
                'score' => round($score, 6),
                'interpretation' => $this->interpretation($score),
            ];
        });

        $average = round($rows->avg('score') ?? 0, 6);

        $clusters = $rows
            ->groupBy(fn (array $row) => $row['result']->cluster_number)
            ->map(function (Collection $items, int $clusterNumber) {
                return [
                    'cluster_number' => $clusterNumber,
                    'cluster_label' => $items->first()['result']->cluster_label,
                    'count' => $items->count(),
                    'average' => round($items->avg('score') ?? 0, 6),
                ];
            })
            ->sortKeys()
            ->values();

        $this->storeScores($run, $rows, $average);

        return [
            'average' => $average,
            'interpretation' => $this->interpretation($average),
            'rows' => $rows,
            'clusters' => $clusters,
        ];
    }

    private function storeScores(ClusteringRun $run, Collection $rows, float $average): void
    {
        $run->forceFill(['silhouette_score' => $average])->save();

        foreach ($rows as $row) {
            $row['result']->forceFill(['silhouette_score' => $row['score']])->save();
        }
    }

    private function distance(array $first, array $second): float
    {
        $sum = 0;

        foreach ($first as $index => $value) {
            $sum += ($value - ($second[$index] ?? 0)) ** 2;
        }

        return sqrt($sum);
    }

    private function interpretation(float $score): string
    {
        if ($score >= 0.71) {
            return 'Struktur cluster kuat';
        }

        if ($score >= 0.51) {
            return 'Struktur cluster baik';
        }

        if ($score >= 0.26) {
            return 'Struktur cluster cukup';
        }

        if ($score >= 0) {
            return 'Struktur cluster lemah';
        }

        return 'Ada data yang lebih dekat ke cluster lain';
    }
}
