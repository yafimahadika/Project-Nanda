<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Normalisasi Data</h1>
            <p class="mt-1 text-sm text-slate-500">Min-Max Normalization untuk menyiapkan atribut sebelum K-Means.</p>
        </div>
    </x-slot>

    @php
        $hasFilters = $search !== '' || $dateFrom || $dateTo;
    @endphp

    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('normalization.index') }}" class="grid gap-3 lg:grid-cols-[minmax(260px,1fr)_180px_180px_auto_auto] lg:items-center">
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
                <i data-lucide="SlidersHorizontal" class="h-4 w-4"></i>
                Filter
            </button>
            @if ($hasFilters)
                <a href="{{ route('normalization.index') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>
    </section>

    <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h2 class="text-base font-semibold text-slate-950">Nilai Minimum dan Maksimum</h2>
            <p class="mt-1 text-sm text-slate-500">Rumus: x' = (x - min) / (max - min). Untuk atribut cost, hasilnya dibalik agar 1 berarti lebih baik.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Atribut</th>
                        <th class="px-5 py-3">Tipe</th>
                        <th class="px-5 py-3">Min</th>
                        <th class="px-5 py-3">Max</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach ($stats as $stat)
                        <tr>
                            <td class="px-5 py-4 text-left font-semibold text-slate-950">{{ $stat['label'] }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $stat['direction'] === 'benefit' ? 'Benefit' : 'Cost' }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($stat['min'], 2) }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ number_format($stat['max'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h2 class="text-base font-semibold text-slate-950">Hasil Normalisasi</h2>
            <p class="mt-1 text-sm text-slate-500">Nilai normalisasi inilah yang digunakan sebagai input jarak Euclidean pada K-Means.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">No</th>
                        <th class="px-5 py-3">Employee ID</th>
                        <th class="px-5 py-3 text-left">Nama</th>
                        @foreach ($attributes as $key => $attribute)
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
                                Belum ada data preprocessing untuk dinormalisasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
