<div class="grid gap-4">
    <div>
        <label for="employee_code_{{ $attendance?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Employee ID</label>
        <select
            id="employee_code_{{ $attendance?->id ?? 'create' }}"
            name="employee_code"
            class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            required
        >
            <option value="">Pilih karyawan terdaftar</option>
            @foreach ($employees as $employee)
                <option
                    value="{{ $employee->employee_id }}"
                    @selected(old('employee_code', $attendance?->employee?->employee_id) === $employee->employee_id)
                >
                    {{ $employee->employee_id }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('employee_code')" class="mt-2" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="attendance_date_{{ $attendance?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Tanggal</label>
            <input
                id="attendance_date_{{ $attendance?->id ?? 'create' }}"
                type="date"
                name="attendance_date"
                value="{{ old('attendance_date', $attendance?->attendance_date?->format('Y-m-d')) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                required
            >
            <x-input-error :messages="$errors->get('attendance_date')" class="mt-2" />
        </div>

        <div>
            <label for="day_{{ $attendance?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Hari</label>
            <input
                id="day_{{ $attendance?->id ?? 'create' }}"
                type="text"
                name="day"
                value="{{ old('day', $attendance?->day) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                placeholder="Monday / Senin"
            >
            <x-input-error :messages="$errors->get('day')" class="mt-2" />
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="shift_{{ $attendance?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Shift</label>
            <input
                id="shift_{{ $attendance?->id ?? 'create' }}"
                type="text"
                name="shift"
                value="{{ old('shift', $attendance?->shift) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            >
            <x-input-error :messages="$errors->get('shift')" class="mt-2" />
        </div>

        <div>
            <label for="attendance_status_{{ $attendance?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Status Absensi</label>
            <input
                id="attendance_status_{{ $attendance?->id ?? 'create' }}"
                type="text"
                name="attendance_status"
                value="{{ old('attendance_status', $attendance?->attendance_status) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                required
            >
            <x-input-error :messages="$errors->get('attendance_status')" class="mt-2" />
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="check_in_{{ $attendance?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Check In</label>
            <input
                id="check_in_{{ $attendance?->id ?? 'create' }}"
                type="time"
                name="check_in"
                value="{{ old('check_in', $attendance?->check_in ? substr($attendance->check_in, 0, 5) : null) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            >
            <x-input-error :messages="$errors->get('check_in')" class="mt-2" />
        </div>

        <div>
            <label for="check_out_{{ $attendance?->id ?? 'create' }}" class="block text-sm font-semibold text-slate-700">Check Out</label>
            <input
                id="check_out_{{ $attendance?->id ?? 'create' }}"
                type="time"
                name="check_out"
                value="{{ old('check_out', $attendance?->check_out ? substr($attendance->check_out, 0, 5) : null) }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            >
            <x-input-error :messages="$errors->get('check_out')" class="mt-2" />
        </div>
    </div>
</div>
