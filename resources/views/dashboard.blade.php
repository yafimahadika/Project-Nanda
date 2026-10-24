<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan data penelitian dan proses clustering.</p>
        </div>
    </x-slot>

    @php
        $cards = [
            ['label' => 'Total Karyawan', 'value' => $totalEmployees, 'icon' => 'Users'],
            ['label' => 'Total Data Absensi', 'value' => $totalAttendances, 'icon' => 'CalendarCheck'],
            ['label' => 'Jumlah Proses Clustering', 'value' => $totalClusteringRuns, 'icon' => 'GitBranch'],
        ];

        $clusterChartData = array_filter($clusterSummary, fn ($count) => $count > 0);
        $attendanceChartData = array_filter($attendanceStatusSummary, fn ($count) => $count > 0);
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-500">{{ $card['label'] }}</div>
                        <div class="mt-3 text-3xl font-semibold text-slate-950">{{ number_format($card['value']) }}</div>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="{{ $card['icon'] }}" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Status Proses Clustering</h2>
                    <p class="mt-1 text-sm text-slate-500">Kategori akan muncul setelah proses K-Means selesai dan centroid akhir diinterpretasikan.</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                @foreach ($clusterSummary as $label => $count)
                    <div class="rounded-md border border-slate-200 p-4">
                        <div class="text-sm text-slate-500">{{ $label }}</div>
                        <div class="mt-2 text-2xl font-semibold text-slate-950">{{ number_format($count) }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-950">Evaluasi Terakhir</h2>
            @if ($latestRun)
                <dl class="mt-5 space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Periode</dt>
                        <dd class="font-medium text-slate-900">{{ $latestRun->period ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Iterasi</dt>
                        <dd class="font-medium text-slate-900">{{ $latestRun->iterations }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Silhouette</dt>
                        <dd class="font-medium text-slate-900">{{ $latestRun->silhouette_score ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Tanggal Proses</dt>
                        <dd class="font-medium text-slate-900">{{ $latestRun->created_at->format('d M Y') }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-500">Belum ada proses clustering yang dijalankan.</p>
            @endif
        </section>
    </div>

    <section class="mt-6 rounded-md border border-cyan-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-slate-950">Ranking Karyawan Terbaik</h2>
                <p class="mt-1 text-sm text-slate-500">Ranking muncul dari hasil proses K-Means terakhir berdasarkan skor normalisasi tertinggi.</p>
            </div>
            <i data-lucide="ArrowUp" class="h-5 w-5 text-cyan-700"></i>
        </div>

        @if ($topEmployees->isNotEmpty())
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Ranking</th>
                            <th class="px-4 py-3">Employee ID</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Skor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($topEmployees as $ranking)
                            <tr>
                                <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ $ranking['rank'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ $ranking['result']->employee->employee_id }}</td>
                                <td class="px-4 py-3 text-left text-slate-700">{{ $ranking['result']->employee->full_name }}</td>
                                <td class="px-4 py-3 text-center text-slate-700">{{ $ranking['result']->cluster_label }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ number_format($ranking['score'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-6 rounded-md border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                Belum ada ranking. Jalankan proses K-Means terlebih dahulu.
            </div>
        @endif
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Diagram Status Absensi</h2>
                    <p class="mt-1 text-sm text-slate-500">Grafik dibuat dari data absensi yang sudah tersimpan.</p>
                </div>
                <i data-lucide="ChartPie" class="h-5 w-5 text-cyan-700"></i>
            </div>

            @if (count($attendanceChartData) > 0)
                <div class="mt-6 h-72">
                    <canvas
                        data-chart="attendance-status"
                        data-labels='@json(array_keys($attendanceChartData))'
                        data-values='@json(array_values($attendanceChartData))'
                    ></canvas>
                </div>
            @else
                <div class="mt-6 rounded-md border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                    Belum ada data absensi untuk ditampilkan.
                </div>
            @endif
        </section>

        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Diagram Hasil Clustering</h2>
                    <p class="mt-1 text-sm text-slate-500">Grafik muncul setelah proses K-Means menghasilkan data.</p>
                </div>
                <i data-lucide="Blocks" class="h-5 w-5 text-cyan-700"></i>
            </div>

            @if (count($clusterChartData) > 0)
                <div class="mt-6 h-72">
                    <canvas
                        data-chart="cluster-result"
                        data-labels='@json(array_keys($clusterChartData))'
                        data-values='@json(array_values($clusterChartData))'
                    ></canvas>
                </div>
            @else
                <div class="mt-6 rounded-md border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                    Belum ada hasil clustering untuk ditampilkan.
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
