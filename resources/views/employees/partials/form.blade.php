<div class="grid gap-4">
    <div>
        <label for="employee_id_{{ $employee?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Employee ID</label>
        <input
            id="employee_id_{{ $employee?->id ?? 'create' }}"
            type="text"
            name="employee_id"
            value="{{ old('employee_id', $employee?->employee_id) }}"
            class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            required
            data-employee-id-check
            data-check-url="{{ route('employees.check-employee-id') }}"
            data-ignore-id="{{ $employee?->id }}"
        >
        <p class="mt-2 hidden text-sm text-red-600" data-employee-id-message></p>
        <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
    </div>

    <div>
        <label for="full_name_{{ $employee?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
        <input
            id="full_name_{{ $employee?->id ?? 'create' }}"
            type="text"
            name="full_name"
            value="{{ old('full_name', $employee?->full_name) }}"
            class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            required
        >
        <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="branch_{{ $employee?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Cabang</label>
            <input
                id="branch_{{ $employee?->id ?? 'create' }}"
                type="text"
                name="branch"
                value="{{ old('branch', $employee?->branch) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            >
            <x-input-error :messages="$errors->get('branch')" class="mt-2" />
        </div>

        <div>
            <label for="job_position_{{ $employee?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Posisi</label>
            <input
                id="job_position_{{ $employee?->id ?? 'create' }}"
                type="text"
                name="job_position"
                value="{{ old('job_position', $employee?->job_position) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            >
            <x-input-error :messages="$errors->get('job_position')" class="mt-2" />
        </div>
    </div>
</div>
