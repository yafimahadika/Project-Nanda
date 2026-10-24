<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Data Absensi</h1>
            <p class="mt-1 text-sm text-slate-500">Import dan kelola absensi berdasarkan Employee ID yang sudah terdaftar.</p>
        </div>
    </x-slot>

    @if (old('form_modal'))
        <div data-open-modal-on-load="{{ old('form_modal') }}"></div>
    @endif

    @php
        $nextDirection = $direction === 'asc' ? 'desc' : 'asc';
        $hasFilters = $search !== '' || $dateFrom || $dateTo || $perPage !== '5';
        $sortUrl = function (string $column) use ($search, $sort, $direction, $nextDirection, $dateFrom, $dateTo, $perPage) {
            return route('attendances.index', [
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'per_page' => $perPage,
                'sort' => $column,
                'direction' => $sort === $column ? $nextDirection : 'asc',
            ]);
        };
    @endphp

    @if (session('alert'))
        <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 transition duration-300" data-auto-dismiss="10000">
            <div class="flex items-start gap-3">
                <i data-lucide="CircleCheck" class="mt-0.5 h-5 w-5 shrink-0"></i>
                <div>{{ session('alert.message') }}</div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 transition duration-300" data-auto-dismiss="10000">
            <div class="flex items-start gap-3">
                <i data-lucide="CircleAlert" class="mt-0.5 h-5 w-5 shrink-0"></i>
                <div>Periksa kembali form absensi. Ada data yang belum sesuai.</div>
            </div>
        </div>
    @endif

    <section class="rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-5">
            <div class="grid gap-3">
            <form method="GET" action="{{ route('attendances.index') }}" class="grid w-full gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_minmax(145px,0.65fr)_minmax(145px,0.65fr)_minmax(125px,0.55fr)_auto_auto] xl:items-center">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <div class="relative flex-1">
                    <i data-lucide="Search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        class="h-11 w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        placeholder="Cari employee ID, nama, status, shift..."
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
                <div class="relative">
                    <select
                        name="per_page"
                        class="h-11 w-full appearance-none rounded-md border-slate-300 bg-white pl-4 pr-10 text-sm font-semibold text-slate-700 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        aria-label="Jumlah data tampil"
                    >
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($perPage === $option)>
                                {{ $option === 'all' ? 'All' : $option }} data
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="ChevronDown" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                </div>
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i data-lucide="Search" class="h-4 w-4"></i>
                    Filter
                </button>
                @if ($hasFilters)
                    <a href="{{ route('attendances.index') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </form>

            <div class="grid gap-3 sm:grid-cols-3 xl:flex xl:flex-wrap xl:items-center xl:justify-end">
                <button type="button" class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-md border border-red-200 bg-red-50 px-3 text-sm font-semibold text-red-700 opacity-60 transition hover:bg-red-100 disabled:cursor-not-allowed" data-bulk-delete-trigger data-modal-open="attendance-bulk-delete-modal" disabled>
                    <i data-lucide="Trash2" class="h-4 w-4"></i>
                    <span>Hapus</span>
                    <span class="rounded bg-red-100 px-1.5 py-0.5 text-xs" data-selected-count>0</span>
                </button>
                <button type="button" class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" data-modal-open="attendance-import-modal">
                    <i data-lucide="Upload" class="h-4 w-4"></i>
                    Import Excel
                </button>
                <button type="button" class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-md bg-cyan-600 px-3 text-sm font-semibold text-white transition hover:bg-cyan-700" data-modal-open="attendance-create-modal" @disabled($employees->isEmpty())>
                    <i data-lucide="Plus" class="h-4 w-4"></i>
                    Tambah Absensi
                </button>
            </div>
            </div>
            <div class="flex flex-wrap gap-2 text-xs text-slate-500">
                <span class="rounded-md bg-slate-100 px-2.5 py-1">Tampil: {{ $perPage === 'all' ? 'Semua data' : $perPage . ' data' }}</span>
                @if ($dateFrom)
                    <span class="rounded-md bg-cyan-50 px-2.5 py-1 text-cyan-700">Dari: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</span>
                @endif
                @if ($dateTo)
                    <span class="rounded-md bg-cyan-50 px-2.5 py-1 text-cyan-700">Sampai: {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>
                @endif
            </div>
        </div>

        @if ($employees->isEmpty())
            <div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-800">
                Data karyawan belum tersedia. Tambah data karyawan terlebih dahulu agar absensi bisa disimpan atau diimport.
            </div>
        @endif

        <div class="overflow-x-auto">
            <form id="attendance-bulk-delete-form" method="POST" action="{{ route('attendances.bulk-destroy', request()->only(['search', 'sort', 'direction', 'date_from', 'date_to', 'per_page', 'page'])) }}">
                @csrf
                @method('DELETE')
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">
                            <div class="flex items-center justify-center gap-3">
                                <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" data-bulk-select-all aria-label="Pilih semua absensi di halaman ini">
                                <span>No</span>
                            </div>
                        </th>
                        <th class="px-5 py-3">Employee ID</th>
                        <th class="px-5 py-3 text-left">Nama</th>
                        @foreach ([
                            'attendance_date' => 'Tanggal',
                            'shift' => 'Shift',
                            'attendance_status' => 'Status',
                        ] as $column => $label)
                            <th class="px-5 py-3">
                                <a href="{{ $sortUrl($column) }}" class="inline-flex items-center gap-1 hover:text-cyan-700">
                                    {{ $label }}
                                    @if ($sort === $column)
                                        <i data-lucide="{{ $direction === 'asc' ? 'ArrowUp' : 'ArrowDown' }}" class="h-3.5 w-3.5"></i>
                                    @else
                                        <i data-lucide="ArrowUpDown" class="h-3.5 w-3.5 text-slate-400"></i>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                        <th class="px-5 py-3">Check In</th>
                        <th class="px-5 py-3">Check Out</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($attendances as $attendance)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-center text-slate-500">
                                <div class="flex items-center justify-center gap-3">
                                    <input type="checkbox" name="attendance_ids[]" value="{{ $attendance->id }}" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" data-bulk-checkbox aria-label="Pilih absensi nomor {{ $attendances->firstItem() + $loop->index }}">
                                    <span>{{ $attendances->firstItem() + $loop->index }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $attendance->employee->employee_id }}</td>
                            <td class="px-5 py-4 text-left text-slate-700">{{ $attendance->employee->full_name }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $attendance->shift ?? '-' }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $attendance->attendance_status }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $attendance->check_in ? substr($attendance->check_in, 0, 5) : '-' }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $attendance->check_out ? substr($attendance->check_out, 0, 5) : '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-center gap-2">
                                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-700" title="Edit" data-modal-open="attendance-edit-modal-{{ $attendance->id }}">
                                        <i data-lucide="Pencil" class="h-4 w-4"></i>
                                    </button>
                                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700" title="Hapus" data-modal-open="attendance-delete-modal-{{ $attendance->id }}">
                                        <i data-lucide="Trash2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada data absensi yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </form>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-slate-500">
                @if ($attendances->total() > 0)
                    Menampilkan {{ $attendances->firstItem() }} - {{ $attendances->lastItem() }} dari {{ $attendances->total() }} data
                @else
                    Menampilkan 0 data
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if ($attendances->onFirstPage())
                    <span class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-400"><i data-lucide="ChevronLeft" class="h-4 w-4"></i> Prev</span>
                @else
                    <a href="{{ $attendances->previousPageUrl() }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"><i data-lucide="ChevronLeft" class="h-4 w-4"></i> Prev</a>
                @endif
                <span class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700">{{ $attendances->currentPage() }} / {{ $attendances->lastPage() }}</span>
                @if ($attendances->hasMorePages())
                    <a href="{{ $attendances->nextPageUrl() }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Next <i data-lucide="ChevronRight" class="h-4 w-4"></i></a>
                @else
                    <span class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-400">Next <i data-lucide="ChevronRight" class="h-4 w-4"></i></span>
                @endif
            </div>
        </div>
    </section>

    <div id="attendance-bulk-delete-modal" class="app-modal hidden" role="dialog" aria-modal="true">
        <div class="app-modal__backdrop" data-modal-close></div>
        <div class="app-modal__panel max-w-md">
            <div class="px-6 py-5">
                <div class="flex gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-red-50 text-red-700">
                        <i data-lucide="Trash2" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Hapus Data Absensi Terpilih?</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            <span data-selected-count>0</span> data absensi yang dicentang akan dihapus dari sistem.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>Batal</button>
                    <button type="submit" form="attendance-bulk-delete-form" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>
        </div>
    </div>

    <div id="attendance-import-modal" class="app-modal hidden" role="dialog" aria-modal="true">
        <div class="app-modal__backdrop" data-modal-close></div>
        <div class="app-modal__panel max-w-lg">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Import Data Absensi</h2>
                    <p class="mt-1 text-sm text-slate-500">Hanya Employee ID yang sudah terdaftar yang akan disimpan.</p>
                </div>
                <button type="button" class="app-modal__close" data-modal-close aria-label="Tutup modal"><i data-lucide="X" class="h-5 w-5"></i></button>
            </div>
            <form method="POST" action="{{ route('attendances.import') }}" enctype="multipart/form-data" class="px-6 py-5">
                @csrf
                <input type="hidden" name="form_modal" value="attendance-import-modal">
                <label for="file" class="block text-sm font-semibold text-slate-700">File Excel</label>
                <input id="file" type="file" name="file" accept=".xlsx,.xls,.csv" class="mt-2 block w-full rounded-md border border-slate-300 text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200" required>
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
                <div class="mt-4 rounded-md bg-slate-50 p-3 text-sm text-slate-600">
                    Kolom yang dibaca: Employee ID, Date, Day, Shift, Attendance, Check In, Check Out.
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>Batal</button>
                    <button type="submit" class="rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Import</button>
                </div>
            </form>
        </div>
    </div>

    <div id="attendance-create-modal" class="app-modal hidden" role="dialog" aria-modal="true">
        <div class="app-modal__backdrop" data-modal-close></div>
        <div class="app-modal__panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Tambah Absensi</h2>
                    <p class="mt-1 text-sm text-slate-500">Absensi hanya bisa disimpan untuk karyawan terdaftar.</p>
                </div>
                <button type="button" class="app-modal__close" data-modal-close aria-label="Tutup modal"><i data-lucide="X" class="h-5 w-5"></i></button>
            </div>
            <form method="POST" action="{{ route('attendances.store') }}" class="px-6 py-5">
                @csrf
                <input type="hidden" name="form_modal" value="attendance-create-modal">
                @include('attendances.partials.form', ['attendance' => null])
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>Batal</button>
                    <button type="submit" class="rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($attendances as $attendance)
        <div id="attendance-edit-modal-{{ $attendance->id }}" class="app-modal hidden" role="dialog" aria-modal="true">
            <div class="app-modal__backdrop" data-modal-close></div>
            <div class="app-modal__panel">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Edit Absensi</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $attendance->employee->employee_id }} - {{ $attendance->employee->full_name }}</p>
                    </div>
                    <button type="button" class="app-modal__close" data-modal-close aria-label="Tutup modal"><i data-lucide="X" class="h-5 w-5"></i></button>
                </div>
                <form method="POST" action="{{ route('attendances.update', array_merge(['attendance' => $attendance], request()->only(['search', 'sort', 'direction', 'date_from', 'date_to', 'per_page', 'page']))) }}" class="px-6 py-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_modal" value="attendance-edit-modal-{{ $attendance->id }}">
                    @include('attendances.partials.form', ['attendance' => $attendance])
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>Batal</button>
                        <button type="submit" class="rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="attendance-delete-modal-{{ $attendance->id }}" class="app-modal hidden" role="dialog" aria-modal="true">
            <div class="app-modal__backdrop" data-modal-close></div>
            <div class="app-modal__panel max-w-md">
                <div class="px-6 py-5">
                    <div class="flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-red-50 text-red-700">
                            <i data-lucide="Trash2" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-950">Hapus Data Absensi?</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Data absensi {{ $attendance->employee->full_name }} tanggal {{ $attendance->attendance_date->format('d M Y') }} akan dihapus.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('attendances.destroy', array_merge(['attendance' => $attendance], request()->only(['search', 'sort', 'direction', 'date_from', 'date_to', 'per_page', 'page']))) }}" class="mt-6 flex justify-end gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>Batal</button>
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
