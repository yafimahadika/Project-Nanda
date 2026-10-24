<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Preprocessing</h1>
            <p class="mt-1 text-sm text-slate-500">Rekap data absensi harian menjadi atribut per karyawan.</p>
        </div>
    </x-slot>

    @php
        $hasFilters = $search !== '' || $dateFrom || $dateTo;
        $cards = [
            ['label' => 'Karyawan Diproses', 'value' => number_format($summary['employees']), 'icon' => 'Users'],
            ['label' => 'Total Data Absensi', 'value' => number_format($summary['records']), 'icon' => 'CalendarCheck'],
            ['label' => 'Rata-rata Kehadiran', 'value' => number_format($summary['average_attendance'], 2) . '%', 'icon' => 'Activity'],
            ['label' => 'Total Menit Terlambat', 'value' => number_format($summary['late_minutes']), 'icon' => 'Clock'],
        ];
    @endphp

    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('preprocessing.index') }}" class="grid gap-3 lg:grid-cols-[minmax(260px,1fr)_180px_180px_auto_auto] lg:items-center">
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
            <div class="relative">
                <i data-lucide="CalendarCheck" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="date"
                    name="date_from"
                    value="{{ $dateFrom }}"
                    class="h-11 w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                    title="Dari tanggal"
                >
            </div>
            <div class="relative">
                <i data-lucide="CalendarCheck" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="date"
                    name="date_to"
                    value="{{ $dateTo }}"
                    class="h-11 w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                    title="Sampai tanggal"
                >
            </div>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                <i data-lucide="ListFilter" class="h-4 w-4"></i>
                Filter
            </button>
            @if ($hasFilters)
                <a href="{{ route('preprocessing.index') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>
    </section>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-500">{{ $card['label'] }}</div>
                        <div class="mt-3 text-2xl font-semibold text-slate-950">{{ $card['value'] }}</div>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-cyan-50 text-cyan-700">
                        <i data-lucide="{{ $card['icon'] }}" class="h-5 w-5"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h2 class="text-base font-semibold text-slate-950">Hasil Preprocessing Absensi</h2>
            <p class="mt-1 text-sm text-slate-500">Data ini yang nanti menjadi dasar normalisasi dan proses K-Means.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">No</th>
                        <th class="px-5 py-3">Employee ID</th>
                        <th class="px-5 py-3 text-left">Nama</th>
                        <th class="px-5 py-3">Total Absensi</th>
                        <th class="px-5 py-3">Hadir</th>
                        <th class="px-5 py-3">Tidak Hadir</th>
                        <th class="px-5 py-3">Izin</th>
                        <th class="px-5 py-3">Sakit</th>
                        <th class="px-5 py-3">Cuti</th>
                        <th class="px-5 py-3">Alpha</th>
                        <th class="px-5 py-3">% Kehadiran</th>
                        <th class="px-5 py-3">Terlambat</th>
                        <th class="px-5 py-3">Menit Terlambat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($rows as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $row['employee_id'] }}</td>
                            <td class="px-5 py-4 text-left text-slate-700">{{ $row['full_name'] }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['total_records']) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['present_count']) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['not_present_count']) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['permission_count']) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['sick_count']) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['leave_count']) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['alpha_count']) }}</td>
                            <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ number_format($row['attendance_percentage'], 2) }}%</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['late_count']) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($row['total_late_minutes']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-5 py-12 text-center text-sm text-slate-500">
                                Belum ada data absensi real untuk diproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
