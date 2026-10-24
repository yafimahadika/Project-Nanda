<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Hasil Clustering</h1>
            <p class="mt-1 text-sm text-slate-500">Lihat hasil pengelompokan karyawan dari proses K-Means yang sudah dijalankan.</p>
        </div>
    </x-slot>

    @php
        $hasFilters = $search !== '' || $cluster !== 'all' || $perPage !== '10' || request('run_id');
        $perPageOptions = ['5' => '5 data', '10' => '10 data', '20' => '20 data', '50' => '50 data', '100' => '100 data', 'all' => 'Semua data'];
    @endphp

    @if (session('success'))
        <div data-auto-dismiss class="mb-5 flex items-start gap-3 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 transition duration-300">
            <i data-lucide="CircleCheck" class="mt-0.5 h-5 w-5 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (! $selectedRun)
        <section class="rounded-md border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            Belum ada hasil clustering. Jalankan proses K-Means terlebih dahulu pada menu Proses K-Means.
        </section>
    @else
        <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('clustering-results.index') }}" class="grid gap-3 2xl:grid-cols-[minmax(240px,1fr)_minmax(240px,1fr)_160px_160px_auto_auto_auto] 2xl:items-center">
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
                <div class="relative">
                    <i data-lucide="Search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        class="h-11 w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        placeholder="Cari employee ID, nama, cabang, posisi..."
                    >
                </div>
                <select name="cluster" class="h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="all" @selected($cluster === 'all')>Semua cluster</option>
                    @foreach ($summary['clusters'] as $item)
                        <option value="{{ $item['number'] }}" @selected((string) $cluster === (string) $item['number'])>
                            C{{ $item['number'] }} - {{ $item['label'] }}
                        </option>
                    @endforeach
                </select>
                <select name="per_page" class="h-11 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    @foreach ($perPageOptions as $value => $label)
                        <option value="{{ $value }}" @selected($perPage === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i data-lucide="ListFilter" class="h-4 w-4"></i>
                    Filter
                </button>
                @if ($hasFilters)
                    <a href="{{ route('clustering-results.index') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                @endif
                <button type="button" data-modal-open="delete-clustering-run-modal" class="inline-flex h-11 items-center justify-center gap-2 rounded-md border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                    <i data-lucide="Trash2" class="h-4 w-4"></i>
                    Hapus Riwayat
                </button>
            </form>
        </section>

        <div id="delete-clustering-run-modal" class="app-modal hidden" role="dialog" aria-modal="true">
            <div class="app-modal__backdrop" data-modal-close></div>
            <div class="app-modal__panel max-w-lg">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-5">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Hapus Riwayat Proses</h2>
                        <p class="mt-1 text-sm text-slate-500">Riwayat proses, centroid, hasil cluster, ranking, dan evaluasi pada proses ini akan dihapus.</p>
                    </div>
                    <button type="button" class="app-modal__close" data-modal-close aria-label="Tutup modal">
                        <i data-lucide="X" class="h-5 w-5"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('clustering-runs.destroy', $selectedRun) }}" class="p-5">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="redirect_to" value="clustering-results.index">
                    <div class="rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                        Yakin hapus proses <span class="font-semibold">{{ $selectedRun->created_at->format('d M Y H:i') }} - {{ $selectedRun->period ?? 'Semua Data' }}</span>?
                    </div>
                    <div class="mt-5 flex justify-end gap-3">
                        <button type="button" data-modal-close class="inline-flex h-10 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-rose-600 px-4 text-sm font-semibold text-white transition hover:bg-rose-700">
                            <i data-lucide="Trash2" class="h-4 w-4"></i>
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
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
                        <div class="text-sm font-medium text-slate-500">Jumlah Cluster</div>
                        <div class="mt-3 text-2xl font-semibold text-slate-950">K = {{ $selectedRun->total_clusters }}</div>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="Blocks" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-500">Total Iterasi</div>
                        <div class="mt-3 text-2xl font-semibold text-slate-950">{{ number_format($selectedRun->iterations) }}</div>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="Activity" class="h-5 w-5"></i>
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

        <section class="mt-6 rounded-md border border-cyan-200 bg-white shadow-sm">
            <div class="border-b border-cyan-100 p-5">
                <h2 class="text-base font-semibold text-slate-950">Ranking Karyawan Terbaik</h2>
                <p class="mt-1 text-sm text-slate-500">Peringkat dihitung dari rata-rata nilai normalisasi. Nilai lebih tinggi berarti performa absensi lebih baik.</p>
            </div>
            <div class="grid gap-4 p-5 lg:grid-cols-3">
                @foreach ($rankings->take(3) as $ranking)
                    <div class="rounded-md border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="rounded-md bg-cyan-50 px-3 py-1 text-sm font-semibold text-cyan-700">Peringkat {{ $ranking['rank'] }}</div>
                            <div class="text-sm font-semibold text-slate-950">{{ number_format($ranking['score'], 2) }}</div>
                        </div>
                        <div class="mt-4 font-semibold text-slate-950">{{ $ranking['result']->employee->full_name }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ $ranking['result']->employee->employee_id }} | {{ $ranking['result']->cluster_label }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
            <section class="rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h2 class="text-base font-semibold text-slate-950">Distribusi Cluster</h2>
                    <p class="mt-1 text-sm text-slate-500">Grafik diambil dari hasil proses yang tersimpan.</p>
                </div>
                <div class="h-72 p-5">
                    @if (collect($summary['counts'])->sum() > 0)
                        <canvas
                            data-chart="cluster-result"
                            data-labels='@json($summary['labels'])'
                            data-values='@json($summary['counts'])'
                        ></canvas>
                    @else
                        <div class="flex h-full items-center justify-center text-sm text-slate-500">Belum ada data hasil clustering.</div>
                    @endif
                </div>
            </section>

            <section class="rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h2 class="text-base font-semibold text-slate-950">Ringkasan Cluster</h2>
                    <p class="mt-1 text-sm text-slate-500">Kategori ditentukan dari rata-rata centroid normalisasi, semakin tinggi semakin baik.</p>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-3 xl:grid-cols-1 2xl:grid-cols-3">
                    @foreach ($summary['clusters'] as $item)
                        <div class="rounded-md border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-slate-950">C{{ $item['number'] }}</div>
                                <div class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-semibold text-cyan-700">{{ number_format($item['count']) }} karyawan</div>
                            </div>
                            <div class="mt-3 text-lg font-semibold text-slate-950">{{ $item['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-950">Tabel Hasil Clustering</h2>
                <p class="mt-1 text-sm text-slate-500">Menampilkan karyawan berdasarkan cluster hasil perhitungan K-Means.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Ranking</th>
                            <th class="px-5 py-3">Employee ID</th>
                            <th class="px-5 py-3 text-left">Nama</th>
                            <th class="px-5 py-3 text-left">Cabang</th>
                            <th class="px-5 py-3 text-left">Posisi</th>
                            <th class="px-5 py-3">Cluster</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Skor</th>
                            <th class="px-5 py-3">Jarak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($results as $result)
                            @php
                                $ranking = $rankByResultId->get($result->id);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-center text-slate-500">{{ $results->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $ranking['rank'] ?? '-' }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $result->employee->employee_id }}</td>
                                <td class="px-5 py-4 text-left text-slate-700">{{ $result->employee->full_name }}</td>
                                <td class="px-5 py-4 text-left text-slate-700">{{ $result->employee->branch }}</td>
                                <td class="px-5 py-4 text-left text-slate-700">{{ $result->employee->job_position }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">C{{ $result->cluster_number }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $result->cluster_label }}</span>
                                </td>
                                <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $ranking ? number_format($ranking['score'], 2) : '-' }}</td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ number_format((float) $result->distance, 6) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-12 text-center text-sm text-slate-500">
                                    Tidak ada data hasil clustering sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    @if ($results->total() > 0)
                        Menampilkan {{ $results->firstItem() }} - {{ $results->lastItem() }} dari {{ $results->total() }} data
                    @else
                        Menampilkan 0 data
                    @endif
                </div>
                <div>{{ $results->links() }}</div>
            </div>
        </section>
    @endif
</x-app-layout>
