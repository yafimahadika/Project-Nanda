<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Proses K-Means</h1>
            <p class="mt-1 text-sm text-slate-500">Jalankan clustering dari data normalisasi absensi real.</p>
        </div>
    </x-slot>

    @php
        $hasFilters = $search !== '' || $dateFrom || $dateTo;
        $attributeKeys = array_keys($attributes);
        $attributeLabels = collect($attributes)->pluck('label')->values();
        $latestHistory = $latestRun?->iteration_history ?? [];
    @endphp

    @if (session('success'))
        <div data-alert class="mb-5 flex items-start gap-3 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i data-lucide="CircleCheck" class="mt-0.5 h-5 w-5 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div data-alert class="mb-5 flex items-start gap-3 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i data-lucide="CircleAlert" class="mt-0.5 h-5 w-5 shrink-0"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div data-alert class="mb-5 flex items-start gap-3 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i data-lucide="CircleAlert" class="mt-0.5 h-5 w-5 shrink-0"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('kmeans.index') }}" class="grid gap-3 xl:grid-cols-[minmax(260px,1fr)_180px_180px_auto_auto] xl:items-center">
            <div class="relative">
                <i data-lucide="Search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    class="h-11 w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                    placeholder="Cari employee ID atau nama..."
                >
            </div>
            <div class="relative">
                <i data-lucide="CalendarCheck" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="h-11 w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
            </div>
            <div class="relative">
                <i data-lucide="CalendarCheck" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="h-11 w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
            </div>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                <i data-lucide="ListFilter" class="h-4 w-4"></i>
                Filter
            </button>
            @if ($hasFilters)
                <a href="{{ route('kmeans.index') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>
    </section>

    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-medium text-slate-500">Data Siap Proses</div>
                    <div class="mt-3 text-2xl font-semibold text-slate-950">{{ number_format($rows->count()) }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                    <i data-lucide="Users" class="h-5 w-5"></i>
                </div>
            </div>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-medium text-slate-500">Jumlah Cluster</div>
                    <div class="mt-3 text-2xl font-semibold text-slate-950">K = 3</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                    <i data-lucide="GitBranch" class="h-5 w-5"></i>
                </div>
            </div>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-medium text-slate-500">Metode Jarak</div>
                    <div class="mt-3 text-xl font-semibold text-slate-950">Euclidean</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                    <i data-lucide="Activity" class="h-5 w-5"></i>
                </div>
            </div>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-medium text-slate-500">Run Terakhir</div>
                    <div class="mt-3 text-xl font-semibold text-slate-950">{{ $latestRun ? $latestRun->created_at->format('d M Y H:i') : '-' }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                    <i data-lucide="Clock" class="h-5 w-5"></i>
                </div>
            </div>
        </div>
    </div>

    <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-950">Input Proses Clustering</h2>
                <p class="mt-1 text-sm text-slate-500">Data berikut berasal dari preprocessing dan normalisasi sesuai filter di atas.</p>
            </div>
            <form method="POST" action="{{ route('kmeans.process') }}">
                @csrf
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">
                <button
                    type="submit"
                    @disabled($rows->count() < 3)
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-cyan-600 px-4 text-sm font-semibold text-white transition hover:bg-cyan-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                >
                    <i data-lucide="GitBranch" class="h-4 w-4"></i>
                    Proses K-Means
                </button>
            </form>
        </div>

        @if ($rows->count() < 3)
            <div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-800">
                Minimal 3 karyawan yang memiliki data absensi diperlukan untuk membentuk K = 3 cluster.
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">No</th>
                        <th class="px-5 py-3">Employee ID</th>
                        <th class="px-5 py-3 text-left">Nama</th>
                        @foreach ($attributes as $attribute)
                            <th class="px-5 py-3">{{ $attribute['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($rows as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $row['employee_id'] }}</td>
                            <td class="px-5 py-4 text-left text-slate-700">{{ $row['full_name'] }}</td>
                            @foreach ($attributes as $key => $attribute)
                                <td class="px-5 py-4 text-center">
                                    <div class="font-semibold text-slate-950">{{ number_format($row['normalized'][$key], 6) }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Asli: {{ number_format($row['original'][$key], 2) }}</div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + count($attributes) }}" class="px-5 py-12 text-center text-sm text-slate-500">
                                Belum ada data normalisasi yang bisa diproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($latestRun)
        <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-950">Hasil Proses Terakhir</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Periode: {{ $latestRun->period ?? 'Semua Data' }} | Data: {{ number_format($latestRun->total_data) }} | Iterasi: {{ number_format($latestRun->iterations) }}
                </p>
            </div>

            <div class="grid gap-4 p-5 md:grid-cols-3">
                @foreach ($latestRun->centroids->sortBy('cluster_number') as $centroid)
                    <div class="rounded-md border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-semibold text-slate-950">C{{ $centroid->cluster_number }}</div>
                            <div class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-semibold text-cyan-700">{{ $centroid->cluster_label }}</div>
                        </div>
                        <div class="mt-4 space-y-2 text-sm">
                            @foreach ($attributeLabels as $index => $label)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-500">{{ $label }}</span>
                                    <span class="font-semibold text-slate-950">{{ number_format($centroid->values[$index] ?? 0, 6) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-950">Hasil Cluster Karyawan</h2>
                <p class="mt-1 text-sm text-slate-500">Cluster ditentukan dari jarak Euclidean paling kecil terhadap centroid akhir.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Employee ID</th>
                            <th class="px-5 py-3 text-left">Nama</th>
                            <th class="px-5 py-3">Cluster</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Jarak Terdekat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($latestRun->results->sortBy(fn ($result) => sprintf('%02d-%s', $result->cluster_number, $result->employee->employee_id)) as $result)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-center text-slate-500">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $result->employee->employee_id }}</td>
                                <td class="px-5 py-4 text-left text-slate-700">{{ $result->employee->full_name }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">C{{ $result->cluster_number }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $result->cluster_label }}</span>
                                </td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ number_format((float) $result->distance, 6) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-950">Detail Iterasi</h2>
                <p class="mt-1 text-sm text-slate-500">Bagian ini membantu mengecek perubahan centroid dan assignment sampai konvergen.</p>
            </div>
            <div class="divide-y divide-slate-200">
                @foreach ($latestHistory as $step)
                    <details class="group p-5" {{ $loop->last ? 'open' : '' }}>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                            <div class="font-semibold text-slate-950">Iterasi {{ $step['iteration'] }}</div>
                            <i data-lucide="ChevronDown" class="h-5 w-5 text-slate-400 transition group-open:rotate-180"></i>
                        </summary>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Karyawan</th>
                                        <th class="px-4 py-3">Jarak C1</th>
                                        <th class="px-4 py-3">Jarak C2</th>
                                        <th class="px-4 py-3">Jarak C3</th>
                                        <th class="px-4 py-3">Cluster Terpilih</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @foreach ($step['distances'] as $pointIndex => $distances)
                                        <tr>
                                            <td class="px-4 py-3 text-left text-slate-700">
                                                <div class="font-semibold text-slate-950">{{ $step['points'][$pointIndex]['employee_id'] ?? 'Data ' . ($pointIndex + 1) }}</div>
                                                <div class="mt-1 text-xs text-slate-500">{{ $step['points'][$pointIndex]['full_name'] ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center text-slate-700">{{ number_format($distances[0] ?? 0, 6) }}</td>
                                            <td class="px-4 py-3 text-center text-slate-700">{{ number_format($distances[1] ?? 0, 6) }}</td>
                                            <td class="px-4 py-3 text-center text-slate-700">{{ number_format($distances[2] ?? 0, 6) }}</td>
                                            <td class="px-4 py-3 text-center font-semibold text-slate-950">C{{ ($step['assignments'][$pointIndex] ?? 0) + 1 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endforeach
            </div>
        </section>
    @endif
</x-app-layout>
