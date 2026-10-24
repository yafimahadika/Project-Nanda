<?php

namespace App\Services;

use Illuminate\Support\Collection;

class NormalizationService
{
    public function normalize(Collection $rows): array
    {
        $attributes = [
            'attendance_percentage' => ['label' => '% Kehadiran', 'direction' => 'benefit'],
            'late_count' => ['label' => 'Jumlah Terlambat', 'direction' => 'cost'],
            'total_late_minutes' => ['label' => 'Menit Terlambat', 'direction' => 'cost'],
            'permission_count' => ['label' => 'Izin', 'direction' => 'cost'],
            'sick_count' => ['label' => 'Sakit', 'direction' => 'cost'],
            'leave_count' => ['label' => 'Cuti', 'direction' => 'cost'],
            'alpha_count' => ['label' => 'Alpha', 'direction' => 'cost'],
        ];

        $stats = collect($attributes)->mapWithKeys(function (array $meta, string $key) use ($rows) {
            return [
                $key => [
                    'label' => $meta['label'],
                    'direction' => $meta['direction'],
                    'min' => (float) $rows->min($key),
                    'max' => (float) $rows->max($key),
                ],
            ];
        })->toArray();

        $normalizedRows = $rows->map(function (array $row) use ($attributes, $stats) {
            $normalized = [];

            foreach ($attributes as $key => $meta) {
                $normalized[$key] = $this->normalizeValue(
                    (float) $row[$key],
                    $stats[$key]['min'],
                    $stats[$key]['max'],
                    $meta['direction']
                );
            }

            return [
                'employee_id' => $row['employee_id'],
                'full_name' => $row['full_name'],
                'original' => collect(array_keys($attributes))
                    ->mapWithKeys(fn (string $key) => [$key => $row[$key]])
                    ->toArray(),
                'normalized' => $normalized,
            ];
        });

        return [
            'attributes' => $attributes,
            'stats' => $stats,
            'rows' => $normalizedRows,
        ];
    }

    private function normalizeValue(float $value, float $min, float $max, string $direction): float
    {
        if ($max === $min) {
            return 1.0;
        }

        $normalized = ($value - $min) / ($max - $min);

        if ($direction === 'cost') {
            $normalized = 1 - $normalized;
        }

        return round($normalized, 6);
    }
}
