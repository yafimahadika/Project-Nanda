<?php

namespace App\Http\Controllers;

use App\Models\ClusteringRun;
use App\Services\DataPreprocessingService;
use App\Services\KMeansService;
use App\Services\NormalizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KMeansController extends Controller
{
    public function index(
        Request $request,
        DataPreprocessingService $preprocessingService,
        NormalizationService $normalizationService
    ): View {
        $search = trim((string) $request->query('search', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $preprocessingRows = $preprocessingService->summarize($dateFrom, $dateTo, $search ?: null);
        $normalization = $normalizationService->normalize($preprocessingRows);

        $latestRun = ClusteringRun::query()
            ->with(['centroids', 'results.employee'])
            ->latest()
            ->first();

        return view('kmeans.index', [
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'attributes' => $normalization['attributes'],
            'rows' => $normalization['rows'],
            'stats' => $normalization['stats'],
            'latestRun' => $latestRun,
        ]);
    }

    public function process(
        Request $request,
        DataPreprocessingService $preprocessingService,
        NormalizationService $normalizationService,
        KMeansService $kMeansService
    ): RedirectResponse {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;

        $preprocessingRows = $preprocessingService->summarize($dateFrom, $dateTo, $search ?: null);
        $normalization = $normalizationService->normalize($preprocessingRows);

        try {
            $run = $kMeansService->run(
                $normalization['rows'],
                $normalization['attributes'],
                $this->periodLabel($dateFrom, $dateTo)
            );
        } catch (\InvalidArgumentException $exception) {
            return redirect()
                ->route('kmeans.index', $request->only(['search', 'date_from', 'date_to']))
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('kmeans.index', $request->only(['search', 'date_from', 'date_to']))
            ->with('success', "Proses K-Means berhasil dijalankan untuk {$run->total_data} karyawan.");
    }

    private function periodLabel(?string $dateFrom, ?string $dateTo): string
    {
        if ($dateFrom && $dateTo) {
            return "{$dateFrom} s/d {$dateTo}";
        }

        if ($dateFrom) {
            return "Mulai {$dateFrom}";
        }

        if ($dateTo) {
            return "Sampai {$dateTo}";
        }

        return 'Semua Data';
    }
}
