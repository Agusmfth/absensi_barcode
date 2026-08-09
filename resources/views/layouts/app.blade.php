<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi Siswa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="{{ request()->routeIs('dashboard') ? 'dashboard-page' : '' }} {{ auth()->user()->isWali() ? 'wali-layout' : '' }}">
<div id="actionLoading" class="action-loading" aria-hidden="true"><div class="loading-box"><span class="spinner-border text-primary"></span><strong>Memproses...</strong><small>Mohon tunggu sebentar</small></div></div>
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content border-0 shadow-lg"><div class="modal-body text-center p-4"><div class="confirm-icon"><i class="bi bi-question-lg"></i></div><h5 class="mt-3 mb-2">Konfirmasi Aksi</h5><p id="confirmActionText" class="text-muted small mb-4">Apakah Anda yakin ingin melanjutkan?</p><div class="d-flex gap-2"><button type="button" class="btn btn-soft w-50" data-bs-dismiss="modal">Batal</button><button type="button" id="confirmActionButton" class="btn btn-primary w-50">Ya, lanjutkan</button></div></div></div></div></div>
<div class="d-flex">
    <aside id="sidebar" class="sidebar p-3 text-white">
        @if(auth()->user()->isWali())
        <nav class="wali-bottom-nav" aria-label="Menu wali kelas">
            <div class="wali-nav-brand"><i class="bi bi-mortarboard-fill"></i><span>Sistem Absensi<small>Wali Kelas</small></span></div>
            <a href="{{ route('dashboard') }}" class="wali-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i><span>Beranda</span>
            </a>
            <a href="{{ route('students.index') }}" class="wali-nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i><span>Siswa</span>
            </a>
            <a href="{{ route('attendance.scan') }}" class="wali-nav-item wali-scan-item {{ request()->routeIs('attendance.scan') ? 'active' : '' }}" aria-label="Scan siswa">
                <span class="wali-scan-button"><i class="bi bi-qr-code-scan"></i></span><span>Scan</span>
            </a>
            <a href="{{ route('attendance.manual') }}" class="wali-nav-item {{ request()->routeIs('attendance.manual') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i><span>Manual</span>
            </a>
            <a href="{{ route('reports.index') }}" class="wali-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i><span>Rekap</span>
            </a>
        </nav>
        @else
        <div class="brand mb-4">
            <i class="bi bi-mortarboard-fill"></i>
            <span>Sistem Absensi<br><small>Siswa Sekolah</small></span>
        </div>

        <nav class="nav flex-column gap-1">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>

            @if (auth()->user()->role !== 'kepala_sekolah')<small class="nav-section">MANAJEMEN</small>@endif
            @if (auth()->user()->isAdmin())
                <a class="nav-link nav-parent" data-bs-toggle="collapse" href="#masterMenu"><i class="bi bi-database"></i> Data Master <i class="bi bi-chevron-down ms-auto"></i></a>
                <div class="collapse nav-submenu {{ request()->routeIs('students.*','teachers.*','classes.*','users.*') ? 'show' : '' }}" id="masterMenu">
                    <a href="{{ route('students.index') }}" class="nav-link"><i class="bi bi-person-vcard"></i> Siswa</a>
                    <a href="{{ route('teachers.index') }}" class="nav-link"><i class="bi bi-person-badge"></i> Guru</a>
                    <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"><i class="bi bi-building"></i> Kelas</a>
                    <a href="{{ route('users.index') }}" class="nav-link"><i class="bi bi-person-gear"></i> Pengguna</a>
                    <a href="{{ route('promotions.index') }}" class="nav-link {{ request()->routeIs('promotions.*') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i> Kenaikan Kelas</a>
                </div>
            @elseif (auth()->user()->role !== 'kepala_sekolah')
                <a href="{{ route('students.index') }}" class="nav-link"><i class="bi bi-people"></i> Siswa Kelas Saya</a>
            @endif
            @if (auth()->user()->role !== 'kepala_sekolah')
                <a class="nav-link nav-parent" data-bs-toggle="collapse" href="#attendanceMenu"><i class="bi bi-qr-code-scan"></i> Absensi <i class="bi bi-chevron-down ms-auto"></i></a>
                <div class="collapse nav-submenu {{ request()->routeIs('attendance.*','admin.student-search','reports.*') ? 'show' : '' }}" id="attendanceMenu">
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.student-search') }}" class="nav-link"><i class="bi bi-search"></i> Cari / Scan Siswa</a>
                    @endif
                    <a href="{{ route('attendance.scan') }}" class="nav-link"><i class="bi bi-camera"></i> {{ auth()->user()->isAdmin() ? 'Buka Landing Scan' : 'Scan Barcode' }}</a>
                    <a href="{{ route('attendance.manual') }}" class="nav-link"><i class="bi bi-pencil-square"></i> Absensi Manual</a>
                    <a href="{{ route('reports.index') }}" class="nav-link"><i class="bi bi-bar-chart-line"></i> Rekap Absensi</a>
                </div>
            @endif
            <small class="nav-section mt-3">LAINNYA</small>
            @if (auth()->user()->role === 'kepala_sekolah')
                <a href="{{ route('reports.index') }}" class="nav-link"><i class="bi bi-bar-chart-line"></i> Rekap Absensi</a>
                <a href="{{ route('account.edit') }}" class="nav-link"><i class="bi bi-person-circle"></i> Akun Saya</a>
            @endif
            @if (auth()->user()->isAdmin())
                <a href="{{ route('holidays.index') }}" class="nav-link"><i class="bi bi-calendar3"></i> Kalender</a>
                <a href="{{ route('settings.index') }}" class="nav-link"><i class="bi bi-gear"></i> Pengaturan</a>
            @endif
        </nav>
        @endif
    </aside>

    <main class="main flex-grow-1">
        <header class="topbar d-flex align-items-center justify-content-between px-3 px-lg-4">
            <button class="btn btn-light d-lg-none" id="menuToggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                <small class="text-muted">{{ str_replace('_', ' ', ucwords(auth()->user()->role, '_')) }}</small>
            </div>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </header>

        <div class="p-3 p-lg-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button class="btn-close" data-bs-dismiss="alert" type="button"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1">@yield('heading')</h4>
                    <small class="text-muted">@yield('subheading')</small>
                </div>
                @yield('actions')
            </div>

            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
