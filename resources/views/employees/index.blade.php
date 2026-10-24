<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-950">Data Karyawan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data dasar karyawan untuk proses absensi dan clustering.</p>
        </div>
    </x-slot>

    @if (old('form_modal'))
        <div data-open-modal-on-load="{{ old('form_modal') }}"></div>
    @endif

    @php
        $nextDirection = $direction === 'asc' ? 'desc' : 'asc';

        $sortUrl = function (string $column) use ($search, $sort, $direction, $nextDirection) {
            return route('employees.index', [
                'search' => $search,
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
                <div>Periksa kembali form karyawan. Ada data yang belum sesuai.</div>
            </div>
        </div>
    @endif

    <section class="rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('employees.index') }}" class="flex w-full flex-col gap-3 sm:flex-row lg:max-w-xl">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <div class="relative flex-1">
                    <i data-lucide="Search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        class="w-full rounded-md border-slate-300 pl-10 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        placeholder="Cari employee ID, nama, cabang, posisi..."
                    >
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i data-lucide="Search" class="h-4 w-4"></i>
                    Cari
                </button>
                @if ($search !== '')
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </form>

            <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700"
                data-modal-open="employee-create-modal"
            >
                <i data-lucide="Plus" class="h-4 w-4"></i>
                Tambah Karyawan
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">No</th>
                        @foreach ([
                            'employee_id' => 'Employee ID',
                            'full_name' => 'Nama Karyawan',
                            'branch' => 'Cabang',
                            'job_position' => 'Posisi',
                        ] as $column => $label)
                            <th class="px-5 py-3 {{ $column === 'full_name' ? 'text-left' : '' }}">
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
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-center text-slate-500">{{ $employees->firstItem() + $loop->index }}</td>
                            <td class="px-5 py-4 text-center font-semibold text-slate-950">{{ $employee->employee_id }}</td>
                            <td class="px-5 py-4 text-left text-slate-700">{{ $employee->full_name }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $employee->branch ?? '-' }}</td>
                            <td class="px-5 py-4 text-center text-slate-700">{{ $employee->job_position ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-center gap-2">
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-700"
                                        title="Edit"
                                        data-modal-open="employee-edit-modal-{{ $employee->id }}"
                                    >
                                        <i data-lucide="Pencil" class="h-4 w-4"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                                        title="Hapus"
                                        data-modal-open="employee-delete-modal-{{ $employee->id }}"
                                    >
                                        <i data-lucide="Trash2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">
                                Belum ada data karyawan yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-slate-500">
                @if ($employees->total() > 0)
                    Menampilkan {{ $employees->firstItem() }} - {{ $employees->lastItem() }} dari {{ $employees->total() }} data
                @else
                    Menampilkan 0 data
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if ($employees->onFirstPage())
                    <span class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-400">
                        <i data-lucide="ChevronLeft" class="h-4 w-4"></i>
                        Prev
                    </span>
                @else
                    <a href="{{ $employees->previousPageUrl() }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="ChevronLeft" class="h-4 w-4"></i>
                        Prev
                    </a>
                @endif

                <span class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700">
                    {{ $employees->currentPage() }} / {{ $employees->lastPage() }}
                </span>

                @if ($employees->hasMorePages())
                    <a href="{{ $employees->nextPageUrl() }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Next
                        <i data-lucide="ChevronRight" class="h-4 w-4"></i>
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-400">
                        Next
                        <i data-lucide="ChevronRight" class="h-4 w-4"></i>
                    </span>
                @endif
            </div>
        </div>
    </section>

    <div id="employee-create-modal" class="app-modal hidden" role="dialog" aria-modal="true">
        <div class="app-modal__backdrop" data-modal-close></div>
        <div class="app-modal__panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Tambah Karyawan</h2>
                    <p class="mt-1 text-sm text-slate-500">Masukkan data dasar karyawan.</p>
                </div>
                <button type="button" class="app-modal__close" data-modal-close aria-label="Tutup modal">
                    <i data-lucide="X" class="h-5 w-5"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('employees.store') }}" class="px-6 py-5">
                @csrf
                <input type="hidden" name="form_modal" value="employee-create-modal">
                @include('employees.partials.form', ['employee' => null])
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>Batal</button>
                    <button type="submit" class="rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($employees as $employee)
        <div id="employee-edit-modal-{{ $employee->id }}" class="app-modal hidden" role="dialog" aria-modal="true">
            <div class="app-modal__backdrop" data-modal-close></div>
            <div class="app-modal__panel">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Edit Karyawan</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $employee->full_name }}</p>
                    </div>
                    <button type="button" class="app-modal__close" data-modal-close aria-label="Tutup modal">
                        <i data-lucide="X" class="h-5 w-5"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('employees.update', array_merge(['employee' => $employee], request()->only(['search', 'sort', 'direction', 'page']))) }}" class="px-6 py-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_modal" value="employee-edit-modal-{{ $employee->id }}">
                    @include('employees.partials.form', ['employee' => $employee])
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>Batal</button>
                        <button type="submit" class="rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="employee-delete-modal-{{ $employee->id }}" class="app-modal hidden" role="dialog" aria-modal="true">
            <div class="app-modal__backdrop" data-modal-close></div>
            <div class="app-modal__panel max-w-md">
                <div class="px-6 py-5">
                    <div class="flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-red-50 text-red-700">
                            <i data-lucide="Trash2" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-950">Hapus Data Karyawan?</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Data <span class="font-semibold text-slate-700">{{ $employee->full_name }}</span> akan dihapus dari sistem.
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('employees.destroy', array_merge(['employee' => $employee], request()->only(['search', 'sort', 'direction', 'page']))) }}" class="mt-6 flex justify-end gap-3">
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
