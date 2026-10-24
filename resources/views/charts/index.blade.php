<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Grafik</h1>
            <p class="mt-1 text-sm text-slate-500">Visualisasi hasil clustering berdasarkan data proses K-Means yang tersimpan.</p>
        </div>
    </x-slot>

    @if (! $selectedRun)
        <section class="rounded-md border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            Belum ada hasil clustering untuk divisualisasikan.
        </section>
    @else
        <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('charts.index') }}" class="grid gap-3 lg:grid-cols-[minmax(260px,1fr)_auto] lg:items-center">
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
            </form>
        </section>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Total Data</div>
                <div class="mt-3 text-2xl font-semibold text-slate-950">{{ number_format($selectedRun->total_data) }}</div>
            </div>
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Nilai Silhouette</div>
                <div class="mt-3 text-2xl font-semibold text-slate-950">{{ number_format($evaluation['average'], 6) }}</div>
            </div>
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Iterasi</div>
                <div class="mt-3 text-2xl font-semibold text-slate-950">{{ number_format($selectedRun->iterations) }}</div>
            </div>
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Periode</div>
                <div class="mt-3 text-base font-semibold text-slate-950">{{ $selectedRun->period ?? 'Semua Data' }}</div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h2 class="text-base font-semibold text-slate-950">Jumlah Karyawan Per Cluster</h2>
                    <p class="mt-1 text-sm text-slate-500">Menampilkan distribusi karyawan pada C1, C2, dan C3.</p>
                </div>
                <div class="h-80 p-5">
                    <canvas data-chart="cluster-result" data-dataset-label="Jumlah Karyawan" data-precision="0" data-labels='@json($summary['labels'])' data-values='@json($summary['counts'])'></canvas>
                </div>
            </section>

            <section class="rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h2 class="text-base font-semibold text-slate-950">Proporsi Cluster</h2>
                    <p class="mt-1 text-sm text-slate-500">Perbandingan persentase setiap cluster dari total data.</p>
                </div>
                <div class="h-80 p-5">
                    <canvas data-chart="attendance-status" data-labels='@json($summary['labels'])' data-values='@json($summary['counts'])'></canvas>
                </div>
            </section>

            <section class="rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h2 class="text-base font-semibold text-slate-950">Rata-rata Jarak Ke Centroid</h2>
                    <p class="mt-1 text-sm text-slate-500">Semakin kecil jaraknya, semakin dekat anggota cluster ke centroid.</p>
                </div>
                <div class="h-80 p-5">
                    <canvas data-chart="cluster-result" data-dataset-label="Rata-rata Jarak" data-precision="2" data-labels='@json($summary['labels'])' data-values='@json($summary['distances'])'></canvas>
                </div>
            </section>

            <section class="rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h2 class="text-base font-semibold text-slate-950">Rata-rata Skor Normalisasi</h2>
                    <p class="mt-1 text-sm text-slate-500">Skor dihitung dari rata-rata atribut normalisasi setiap cluster.</p>
                </div>
                <div class="h-80 p-5">
                    <canvas data-chart="cluster-result" data-dataset-label="Rata-rata Skor" data-precision="2" data-labels='@json($summary['labels'])' data-values='@json($summary['scores'])'></canvas>
                </div>
            </section>
        </div>
    @endif
</x-app-layout>
