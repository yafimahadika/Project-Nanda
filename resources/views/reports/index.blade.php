<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Laporan</h1>
            <p class="mt-1 text-sm text-slate-500">Rekap hasil K-Means untuk dokumentasi dan cetak laporan.</p>
        </div>
    </x-slot>

    @if (! $selectedRun)
        <section class="rounded-md border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            Belum ada hasil clustering untuk dibuat laporan.
        </section>
    @else
        <section class="mb-6 rounded-md border border-slate-200 bg-white p-5 shadow-sm print:hidden">
            <form method="GET" action="{{ route('reports.index') }}" class="grid gap-3 lg:grid-cols-[minmax(260px,1fr)_auto_auto] lg:items-center">
                <div class="relative">
                    <i data-lucide="Clock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <select name="run_id" class="h-11 w-full rounded-md border-slate-300 pl-10 pr-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                        @foreach ($runs as $run)
                            <option value="{{ $run->id }}" @selected($selectedRun->id === $run->id)>
                                {{ $run->created_at->format('d M Y H:i') }} - {{ $run->period ?? 'Semua Data' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i data-lucide="ListFilter" class="h-4 w-4"></i>
                    Tampilkan
                </button>
                <button type="button" onclick="window.print()" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-cyan-600 px-4 text-sm font-semibold text-white transition hover:bg-cyan-700">
                    <i data-lucide="FileText" class="h-4 w-4"></i>
                    Cetak
                </button>
            </form>
        </section>

        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
            <div class="border-b border-slate-200 pb-5 text-center">
                <h2 class="text-xl font-semibold text-slate-950">Laporan Hasil Clustering Karyawan</h2>
                <p class="mt-2 text-sm text-slate-600">Sistem Pendukung Pengelompokan Karyawan Terbaik - Biznet Karawang</p>
            </div>

            <div class="mt-6 grid gap-4 text-sm md:grid-cols-2">
                <div class="rounded-md border border-slate-200 p-4">
                    <div class="font-semibold text-slate-950">Informasi Proses</div>
                    <div class="mt-3 space-y-2 text-slate-700">
                        <div class="flex justify-between gap-4"><span>Periode</span><span class="font-semibold">{{ $selectedRun->period ?? 'Semua Data' }}</span></div>
                        <div class="flex justify-between gap-4"><span>Tanggal Proses</span><span class="font-semibold">{{ $selectedRun->created_at->format('d M Y H:i') }}</span></div>
                        <div class="flex justify-between gap-4"><span>Jumlah Data</span><span class="font-semibold">{{ number_format($selectedRun->total_data) }}</span></div>
                        <div class="flex justify-between gap-4"><span>Jumlah Cluster</span><span class="font-semibold">K = {{ $selectedRun->total_clusters }}</span></div>
                    </div>
                </div>
                <div class="rounded-md border border-slate-200 p-4">
                    <div class="font-semibold text-slate-950">Evaluasi</div>
                    <div class="mt-3 space-y-2 text-slate-700">
                        <div class="flex justify-between gap-4"><span>Iterasi</span><span class="font-semibold">{{ number_format($selectedRun->iterations) }}</span></div>
                        <div class="flex justify-between gap-4"><span>Silhouette</span><span class="font-semibold">{{ number_format($evaluation['average'], 6) }}</span></div>
                        <div class="flex justify-between gap-4"><span>Interpretasi</span><span class="font-semibold">{{ $evaluation['interpretation'] }}</span></div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-base font-semibold text-slate-950">Ranking Karyawan Terbaik</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Ranking</th>
                                <th class="px-4 py-3">Employee ID</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3">Cluster</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Skor Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($rankings as $ranking)
                                <tr>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ $ranking['rank'] }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ $ranking['result']->employee->employee_id }}</td>
                                    <td class="px-4 py-3 text-left text-slate-700">{{ $ranking['result']->employee->full_name }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">C{{ $ranking['result']->cluster_number }}</td>
                                    <td class="px-4 py-3 text-center text-slate-700">{{ $ranking['result']->cluster_label }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ number_format($ranking['score'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-base font-semibold text-slate-950">Centroid Akhir</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Cluster</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Nilai Centroid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($selectedRun->centroids->sortBy('cluster_number') as $centroid)
                                <tr>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">C{{ $centroid->cluster_number }}</td>
                                    <td class="px-4 py-3 text-center text-slate-700">{{ $centroid->cluster_label }}</td>
                                    <td class="px-4 py-3 text-center text-slate-700">{{ collect($centroid->values)->map(fn ($value) => number_format($value, 6))->implode(' | ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-base font-semibold text-slate-950">Daftar Hasil Clustering</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Ranking</th>
                                <th class="px-4 py-3">Employee ID</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Posisi</th>
                                <th class="px-4 py-3">Cluster</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Skor</th>
                                <th class="px-4 py-3">Jarak</th>
                                <th class="px-4 py-3">Silhouette</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($rankings as $ranking)
                                @php($result = $ranking['result'])
                                <tr>
                                    <td class="px-4 py-3 text-center text-slate-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ $ranking['rank'] }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ $result->employee->employee_id }}</td>
                                    <td class="px-4 py-3 text-left text-slate-700">{{ $result->employee->full_name }}</td>
                                    <td class="px-4 py-3 text-left text-slate-700">{{ $result->employee->job_position }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">C{{ $result->cluster_number }}</td>
                                    <td class="px-4 py-3 text-center text-slate-700">{{ $result->cluster_label }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">{{ number_format($ranking['score'], 2) }}</td>
                                    <td class="px-4 py-3 text-center text-slate-700">{{ number_format((float) $result->distance, 6) }}</td>
                                    <td class="px-4 py-3 text-center text-slate-700">{{ number_format((float) $result->silhouette_score, 6) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
</x-app-layout>
