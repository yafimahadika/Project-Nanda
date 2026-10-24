<?php

namespace App\Services;

use App\Models\ClusteringRun;
use Illuminate\Support\Collection;

class EmployeeRankingService
{
    public function rank(ClusteringRun $run): Collection
    {
        return $run->results
            ->map(function ($result) {
                $score = collect($result->normalized_values ?? [])->avg() ?? 0;

                return [
                    'result' => $result,
                    'score' => round($score * 100, 2),
                    'raw_score' => $score,
                    'distance' => (float) $result->distance,
                ];
            })
            ->sortBy([
                ['raw_score', 'desc'],
                ['distance', 'asc'],
            ])
            ->values()
            ->map(function (array $row, int $index) {
                $row['rank'] = $index + 1;

                return $row;
            });
    }
}
