<?php

namespace App\Http\Controllers;

use App\Services\DataPreprocessingService;
use App\Services\NormalizationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NormalizationController extends Controller
{
    public function __invoke(
        Request $request,
        DataPreprocessingService $preprocessingService,
        NormalizationService $normalizationService
    ): View {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = trim((string) $request->query('search'));

        $preprocessingRows = $preprocessingService->summarize($dateFrom, $dateTo, $search ?: null);
        $normalization = $normalizationService->normalize($preprocessingRows);

        return view('normalization.index', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
            'attributes' => $normalization['attributes'],
            'stats' => $normalization['stats'],
            'rows' => $normalization['rows'],
        ]);
    }
}
