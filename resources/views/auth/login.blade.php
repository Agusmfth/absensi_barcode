<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{$school?->school_name ?: 'Sistem Absensi Siswa'}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
<div id="actionLoading" class="action-loading" aria-hidden="true"><div class="loading-box"><span class="spinner-border text-primary"></span><strong>Memeriksa akun...</strong><small>Mohon tunggu sebentar</small></div></div>
<div id="loginSuccess" class="login-success" aria-hidden="true"><div class="login-success-box"><div class="success-check"><span></span><i class="bi bi-check-lg"></i></div><h4>Login Berhasil</h4><p>Selamat datang, mengalihkan ke dashboard...</p><div class="success-progress"><i></i></div></div></div>
<div id="loginFailure" class="login-failure" aria-hidden="true"><div class="login-failure-box"><div class="failure-cross"><i class="bi bi-x-lg"></i></div><h4>Login Gagal</h4><p>Periksa kembali akun dan kata sandi Anda.</p></div></div>
<main class="auth-shell">
    <section class="auth-showcase">
        <div class="showcase-circle circle-one"></div><div class="showcase-circle circle-two"></div>
        <div class="school-mark"><span>@if($school?->school_logo)<img src="{{asset('storage/'.$school->school_logo)}}" alt="Logo {{$school->school_name}}">@else<i class="bi bi-mortarboard-fill"></i>@endif</span><div><strong>Sistem Absensi Siswa</strong><small>{{$school?->school_name ?: 'Sekolah'}}</small></div></div>
        <div class="showcase-content">
            <span class="eyebrow">SISTEM INFORMASI SEKOLAH</span>
            <h1>Kehadiran siswa<br>lebih mudah dikelola.</h1>
            <p>Catat kehadiran secara cepat melalui QR Code, pantau rekap kelas, dan hasilkan laporan dalam satu dashboard terpadu.</p>
            <div class="feature-list">
                <div><i class="bi bi-qr-code-scan"></i><span><strong>Absensi Cepat</strong><small>Scan QR melalui kamera perangkat</small></span></div>
                <div><i class="bi bi-shield-check"></i><span><strong>Akses Terlindungi</strong><small>Hak akses sesuai peran pengguna</small></span></div>
                <div><i class="bi bi-bar-chart-line"></i><span><strong>Laporan Lengkap</strong><small>Rekap Excel dan PDF siap cetak</small></span></div>
            </div>
        </div>
        <small class="showcase-footer">© {{ date('Y') }} Sistem Absensi Siswa Sekolah</small>
    </section>

    <section class="auth-form-panel">
        <div class="auth-form-wrap">
            <div class="mobile-logo">@if($school?->school_logo)<img src="{{asset('storage/'.$school->school_logo)}}" alt="Logo {{$school->school_name}}">@else<i class="bi bi-mortarboard-fill"></i>@endif</div>
            <span class="welcome-label">SELAMAT DATANG</span>
            <h2>Masuk ke akun Anda</h2>
            <p class="auth-subtitle">Gunakan email atau username yang telah terdaftar.</p>

            @if ($errors->any())
                <div class="alert alert-danger d-flex gap-2"><i class="bi bi-exclamation-circle"></i><span>{{ $errors->first() }}</span></div>
            @endif
            <div id="loginError" class="alert alert-danger d-flex gap-2 d-none"><i class="bi bi-exclamation-circle"></i><span></span></div>

            <form method="post" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email atau Username</label>
                    <div class="input-group auth-input"><span class="input-group-text"><i class="bi bi-person"></i></span><input class="form-control" name="login" value="{{ old('login') }}" placeholder="Masukkan email atau username" required autofocus></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kata Sandi</label>
                    <div class="input-group auth-input"><span class="input-group-text"><i class="bi bi-lock"></i></span><input id="password" type="password" class="form-control" name="password" placeholder="Masukkan kata sandi" required><button class="btn password-toggle" type="button" onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password';this.querySelector('i').classList.toggle('bi-eye-slash')"><i class="bi bi-eye"></i></button></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4"><label class="form-check-label"><input class="form-check-input me-2" type="checkbox" name="remember">Ingat saya</label><span class="text-primary small">Hubungi administrator jika lupa sandi</span></div>
                <button class="btn btn-primary btn-login w-100"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
            </form>
            <div class="demo-note"><i class="bi bi-info-circle"></i><span>Akun demo Admin: <strong>admin@sekolah.test</strong> · password: <strong>password</strong></span></div>
        </div>
    </section>
</main>
</body>
</html>
