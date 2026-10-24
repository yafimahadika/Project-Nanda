<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800">
        <div class="app-shell min-h-screen bg-slate-100 lg:flex">
            <aside class="app-sidebar bg-slate-950 text-white lg:fixed lg:inset-y-0">
                <div class="flex h-full flex-col">
                    <div class="flex min-h-[69px] items-center gap-3 border-b border-white/10 px-4 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-cyan-500 text-white">
                            <i data-lucide="Network" class="h-5 w-5"></i>
                        </div>
                        <div class="sidebar-label min-w-0">
                            <div class="truncate text-lg font-semibold">Biznet K-Means</div>
                            <div class="mt-1 truncate text-sm text-slate-300">Admin / HRD</div>
                        </div>
                    </div>

                    @php
                        $navigation = [
                            ['label' => 'Dashboard', 'icon' => 'LayoutDashboard', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                            ['label' => 'Data Karyawan', 'icon' => 'Users', 'route' => 'employees.index', 'active' => request()->routeIs('employees.*')],
                            ['label' => 'Data Absensi', 'icon' => 'CalendarCheck', 'route' => 'attendances.index', 'active' => request()->routeIs('attendances.*')],
                            ['label' => 'Preprocessing', 'icon' => 'ListFilter', 'route' => 'preprocessing.index', 'active' => request()->routeIs('preprocessing.*')],
                            ['label' => 'Normalisasi Data', 'icon' => 'SlidersHorizontal', 'route' => 'normalization.index', 'active' => request()->routeIs('normalization.*')],
                            ['label' => 'Proses K-Means', 'icon' => 'GitBranch', 'route' => 'kmeans.index', 'active' => request()->routeIs('kmeans.*')],
                            ['label' => 'Hasil Clustering', 'icon' => 'Blocks', 'route' => 'clustering-results.index', 'active' => request()->routeIs('clustering-results.*')],
                            ['label' => 'Evaluasi Silhouette', 'icon' => 'Activity', 'route' => 'silhouette.index', 'active' => request()->routeIs('silhouette.*')],
                            ['label' => 'Grafik', 'icon' => 'ChartPie', 'route' => 'charts.index', 'active' => request()->routeIs('charts.*')],
                            ['label' => 'Laporan', 'icon' => 'FileText', 'route' => 'reports.index', 'active' => request()->routeIs('reports.*')],
                        ];
                    @endphp

                    <nav class="flex-1 space-y-1 px-3 py-5">
                        @foreach ($navigation as $item)
                            <a
                                href="{{ isset($item['route']) ? route($item['route']) : $item['url'] }}"
                                class="sidebar-link {{ $item['active'] ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                                title="{{ $item['label'] }}"
                            >
                                <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5 shrink-0"></i>
                                <span class="sidebar-label truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="border-t border-white/10 p-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="sidebar-link w-full bg-white text-slate-950 hover:bg-slate-200" title="Logout">
                                <i data-lucide="LogOut" class="h-5 w-5 shrink-0"></i>
                                <span class="sidebar-label truncate">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="app-content min-w-0 min-h-screen flex-1">
                <header class="border-b border-slate-200 bg-white">
                    <div class="flex flex-col gap-3 px-6 py-5 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                        <div class="flex items-start gap-4">
                            <button type="button" class="sidebar-toggle" data-sidebar-toggle aria-label="Tutup sidebar" title="Buka/tutup sidebar">
                                <i data-lucide="PanelLeftClose" class="sidebar-toggle-icon h-5 w-5"></i>
                            </button>
                            <div>
                                @if (isset($header))
                                    {{ $header }}
                                @else
                                    <h1 class="text-xl font-semibold text-slate-950">Dashboard</h1>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-slate-500">
                            {{ Auth::user()->name }}
                        </div>
                    </div>
                </header>

                <main class="px-6 py-8 lg:px-8">
                    {{ $slot }}
                </main>

                <footer class="border-t border-slate-200 px-6 py-4 text-sm text-slate-500 lg:px-8">
                    Sistem Pendukung Pengelompokan Karyawan Terbaik - Biznet Karawang
                </footer>
            </div>
        </div>
    </body>
</html>
