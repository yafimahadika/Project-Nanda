<x-guest-layout>
    <div class="office-login-shell">
        <section class="office-login-brand">
            <div class="office-login-brand__content">
                <img src="{{ asset('images/logo-biznet.png') }}" alt="Biznet" class="office-login-logo">

                <div class="office-login-copy">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-700">Internal HRD Portal</p>
                    <h1 class="mt-4 text-4xl font-semibold leading-tight text-slate-950 lg:text-5xl">
                        Sistem Pengelompokan Karyawan Terbaik
                    </h1>
                    <p class="mt-5 text-base leading-7 text-slate-600">
                        Aplikasi penelitian untuk mengelola data karyawan, absensi, kepuasan pelanggan, dan proses K-Means di Biznet Karawang.
                    </p>
                </div>

                <div></div>
            </div>
        </section>

        <section class="office-login-panel">
            <div class="office-login-card">
                <div class="mb-8">
                    <div class="inline-flex rounded-md bg-cyan-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                        Secure Login
                    </div>
                    <h2 class="mt-5 text-2xl font-semibold text-slate-950">Masuk ke Dashboard</h2>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                        <input
                            id="email"
                            class="office-login-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@biznet.local"
                        >
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                        <div class="relative mt-2">
                            <input
                                id="password"
                                class="office-login-input pr-24"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                            >
                            <button
                                type="button"
                                class="office-password-toggle"
                                data-password-toggle="password"
                                aria-label="Tampilkan password"
                            >
                                Tampilkan
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
                            <span class="ms-2 text-sm text-slate-600">Ingat sesi login</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-semibold text-cyan-700 hover:text-cyan-900" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="office-login-button">
                        Masuk
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-guest-layout>
