<?php

namespace App\Http\Controllers;

use App\Models\ClusteringRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClusteringRunController extends Controller
{
    public function destroy(Request $request, ClusteringRun $clusteringRun): RedirectResponse
    {
        $clusteringRun->delete();

        return redirect()
            ->route($request->input('redirect_to', 'clustering-results.index'))
            ->with('success', 'Riwayat proses K-Means berhasil dihapus.');
    }
}
