<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Evaluasi Silhouette</h1>
            <p class="mt-1 text-sm text-slate-500">Mengukur kualitas hasil cluster berdasarkan kedekatan antar data.</p>
        </div>
    </x-slot>

    @if (! $selectedRun)
        <section class="rounded-md border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            Belum ada hasil clustering. Jalankan proses K-Means terlebih dahulu sebelum evaluasi.
        </section>
    @else
        <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('silhouette.index') }}" class="grid gap-3 lg:grid-cols-[minmax(260px,1fr)_auto] lg:items-center">
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
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-500">Nilai Silhouette</div>
                        <div class="mt-3 text-2xl font-semibold text-slate-950">{{ number_format($evaluation['average'], 6) }}</div>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="Activity" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-500">Interpretasi</div>
                        <div class="mt-3 text-lg font-semibold text-slate-950">{{ $evaluation['interpretation'] }}</div>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="CircleCheck" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-500">Total Data</div>
                        <div class="mt-3 text-2xl font-semibold text-slate-950">{{ number_format($selectedRun->total_data) }}</div>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="Users" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-500">Periode</div>
                        <div class="mt-3 text-base font-semibold text-slate-950">{{ $selectedRun->period ?? 'Semua Data' }}</div>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="CalendarCheck" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-950">Rata-rata Silhouette Per Cluster</h2>
                <p class="mt-1 text-sm text-slate-500">Semakin mendekati 1, semakin baik pemisahan dan kepadatan cluster.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Cluster</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Jumlah Data</th>
                            <th class="px-5 py-3">Rata-rata Silhouette</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($evaluation['clusters'] as $cluster)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">C{{ $cluster['cluster_number'] }}</td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ $cluster['cluster_label'] }}</td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ number_format($cluster['count']) }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ number_format($cluster['average'], 6) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-950">Detail Evaluasi Per Karyawan</h2>
                <p class="mt-1 text-sm text-slate-500">a(i) adalah jarak rata-rata dalam cluster, b(i) adalah jarak rata-rata terdekat ke cluster lain.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Employee ID</th>
                            <th class="px-5 py-3 text-left">Nama</th>
                            <th class="px-5 py-3">Cluster</th>
                            <th class="px-5 py-3">a(i)</th>
                            <th class="px-5 py-3">b(i)</th>
                            <th class="px-5 py-3">s(i)</th>
                            <th class="px-5 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($evaluation['rows'] as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-center text-slate-500">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $row['result']->employee->employee_id }}</td>
                                <td class="px-5 py-4 text-left text-slate-700">{{ $row['result']->employee->full_name }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">C{{ $row['result']->cluster_number }}</td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['a'], 6) }}</td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['b'], 6) }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ number_format($row['score'], 6) }}</td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ $row['interpretation'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</x-app-layout>
