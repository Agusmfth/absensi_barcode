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
    @if(auth()->user()->isWali())
    <style>
        body.wali-layout .sidebar{display:block!important;width:100%!important;min-height:0!important;height:76px!important;position:fixed!important;inset:auto 0 0!important;padding:0!important;background:transparent!important;transform:none!important;z-index:1050!important}
        body.wali-layout .main{margin-left:0!important;padding-bottom:94px;width:100%;min-width:0}
        body.wali-layout .topbar{position:sticky;top:0;z-index:1020}
        body.wali-layout #menuToggle{display:none!important}
        .wali-bottom-nav{width:min(680px,calc(100% - 24px));height:68px;margin:0 auto 8px;display:grid!important;grid-template-columns:repeat(5,minmax(0,1fr));align-items:center;padding:5px 8px;background:#fff;border:1px solid #e5ebf4;border-radius:20px;box-shadow:0 10px 35px #092b6230}
        .wali-nav-item{height:55px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;color:#8794a7;text-decoration:none;font-size:.65rem;font-weight:650;border-radius:14px}
        .wali-nav-item>i{font-size:1.18rem}.wali-nav-item.active{color:#146ee8;background:#edf5ff}
        .wali-scan-item{position:relative;overflow:visible;color:#146ee8}.wali-scan-item.active{background:transparent}
        .wali-scan-button{width:58px;height:58px;margin-top:-31px;display:grid;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(145deg,#176fe8,#0b4fc2);border:5px solid #f4f7fb;box-shadow:0 8px 20px #146ee84d}
        .wali-scan-button i{font-size:1.45rem}.wali-scan-item>span:last-child{margin-top:1px}
        .wali-scan-item.active .wali-scan-button{box-shadow:0 8px 22px #146ee866,0 0 0 3px #bcd7ff}
        @media(max-width:575.98px){body.wali-layout .main{padding-bottom:88px}.wali-bottom-nav{width:calc(100% - 16px);border-radius:18px}.manual-attendance-card .student-table{min-width:0;width:100%}.manual-attendance-card thead{display:none}.manual-attendance-card tbody,.manual-attendance-card tr{display:block;width:100%}.manual-attendance-card tbody tr{padding:14px;border-bottom:1px solid #edf0f4}.manual-attendance-card tbody td{display:block;width:100%;padding:5px 0;border:0}.manual-attendance-card tbody td:first-child{padding-bottom:10px}.manual-attendance-card tbody td[data-label]:before{content:attr(data-label);display:block;margin-bottom:5px;color:#748299;font-size:.65rem;font-weight:700;text-transform:uppercase}.manual-attendance-card .form-select,.manual-attendance-card .form-control{width:100%;max-width:100%;min-width:0}.manual-attendance-card .card-footer .btn{width:100%}}
    </style>
    @endif
    @stack('head')
</head>
<body class="{{ request()->routeIs('dashboard') ? 'dashboard-page' : '' }} {{ auth()->user()->isWali() ? 'wali-layout' : '' }}">
<div id="actionLoading" class="action-loading" aria-hidden="true"><div class="loading-box"><span class="spinner-border text-primary"></span><strong>Memproses...</strong><small>Mohon tunggu sebentar</small></div></div>
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content border-0 shadow-lg"><div class="modal-body text-center p-4"><div class="confirm-icon"><i class="bi bi-question-lg"></i></div><h5 class="mt-3 mb-2">Konfirmasi Aksi</h5><p id="confirmActionText" class="text-muted small mb-4">Apakah Anda yakin ingin melanjutkan?</p><div class="d-flex gap-2"><button type="button" class="btn btn-soft w-50" data-bs-dismiss="modal">Batal</button><button type="button" id="confirmActionButton" class="btn btn-primary w-50">Ya, lanjutkan</button></div></div></div></div></div>
<div class="d-flex">
    <aside id="sidebar" class="sidebar p-3 text-white">
        @if(auth()->user()->isWali())
        <nav class="wali-bottom-nav" aria-label="Menu wali kelas">
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
