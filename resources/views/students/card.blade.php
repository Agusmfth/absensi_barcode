<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu {{$student->name}}</title>
    <style>
        *{box-sizing:border-box}body{margin:0;padding:18px;background:#e8edf3;font-family:Arial,sans-serif;color:#12344f}.toolbar{text-align:center;margin-bottom:18px}.toolbar button,.toolbar a{display:inline-block;margin:0 3px;padding:8px 13px;border:0;border-radius:7px;background:#087eb9;color:#fff;text-decoration:none;font-size:13px;cursor:pointer}.student-card{width:85.6mm;height:53.98mm;margin:auto;position:relative;overflow:hidden;border:1px solid #8fd2e7;border-radius:3mm;background-color:#c9f3fc;background-image:linear-gradient(155deg,rgba(255,255,255,.75) 0 29%,transparent 29%),linear-gradient(25deg,rgba(31,184,218,.18) 0 24%,transparent 24%),linear-gradient(115deg,#dffaff,#b8ebf7 58%,#d8f8fb);box-shadow:0 10px 28px rgba(22,80,105,.2);-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}.shape-one{position:absolute;width:45mm;height:45mm;right:-19mm;top:-23mm;border-radius:50%;background:rgba(10,155,194,.12)}.shape-two{position:absolute;width:42mm;height:13mm;left:17mm;bottom:-8mm;transform:rotate(-12deg);background:rgba(255,255,255,.3)}
        .card-header{height:15.5mm;padding:2.4mm 3.5mm 1.5mm;position:relative;z-index:2;border-bottom:.65mm solid rgba(8,126,185,.45)}.header-table{width:100%;border-collapse:collapse}.header-logo{width:11.5mm;vertical-align:middle}.school-logo{display:block;width:9.5mm;height:9.5mm;object-fit:contain}.logo-fallback{width:9.5mm;height:9.5mm;line-height:9.5mm;text-align:center;border-radius:50%;background:#fff;color:#0988ba;font-size:5mm}.school-info{vertical-align:middle}.school-info strong{display:block;max-width:38mm;font-size:2.35mm;line-height:1.15;text-transform:uppercase}.school-info small{display:block;max-width:38mm;margin-top:.6mm;font-size:1.55mm;line-height:1.2;color:#47768c}.card-title{text-align:right;vertical-align:middle;color:#087ead}.card-title strong{display:block;font-size:5.6mm;line-height:.9;letter-spacing:.02em;text-shadow:0 1px 0 #fff}.card-title span{font-size:1.55mm;font-weight:700;letter-spacing:.11em}
        .watermark{position:absolute;z-index:0;left:34mm;top:23mm;width:22mm;height:22mm;object-fit:contain;opacity:.07}.card-content{height:38.48mm;padding:3mm 3.5mm;position:relative;z-index:2}.content-table{width:100%;height:100%;border-collapse:collapse}.photo-cell{width:19mm;vertical-align:middle}.student-photo,.photo-placeholder{display:block;width:17mm;height:23mm;border:.7mm solid rgba(255,255,255,.95);border-radius:1.3mm;background:#fff;object-fit:cover;box-shadow:0 2px 7px rgba(17,83,108,.2)}.photo-placeholder{text-align:center;line-height:21.6mm;color:#7ca6b8;font-size:7mm}.data-cell{padding:0 2.3mm;vertical-align:middle}.student-data{width:100%;border-collapse:collapse;font-size:2.05mm;line-height:1.6}.student-data td:first-child{width:15mm;color:#174963;white-space:nowrap}.student-data td:nth-child(2){width:2mm;text-align:center}.student-data td:last-child{max-width:23mm;font-weight:700;color:#0e334b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.qr-cell{width:20mm;text-align:right;vertical-align:bottom}.qr-box{display:inline-block;width:18.5mm;height:18.5mm;padding:1.15mm;background:#fff;border:1px solid #8cc9dc;border-radius:1.3mm;box-shadow:0 2px 7px rgba(17,83,108,.16)}.qr-box svg{display:block;width:16mm;height:16mm}.qr-caption{margin-top:.7mm;text-align:center;color:#28738e;font-size:1.35mm;font-weight:700;letter-spacing:.05em}.footer-brand{position:absolute;z-index:2;left:24.5mm;bottom:2mm;color:#0787b6;font-size:1.55mm;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
        @page{size:85.6mm 53.98mm;margin:0}@media print{html,body{width:85.6mm!important;height:53.98mm!important;margin:0!important;padding:0!important;overflow:hidden;background:#fff}.toolbar{display:none}.student-card{position:absolute;left:0;top:0;width:85.6mm!important;height:53.98mm!important;margin:0!important;border:0;border-radius:0;box-shadow:none;transform:none!important;background-color:#c9f3fc!important;background-image:linear-gradient(155deg,rgba(255,255,255,.75) 0 29%,transparent 29%),linear-gradient(25deg,rgba(31,184,218,.18) 0 24%,transparent 24%),linear-gradient(115deg,#dffaff,#b8ebf7 58%,#d8f8fb)!important}}
        .card-header{height:16.5mm;padding-bottom:1.8mm}.card-content{height:37.48mm}.card-title{white-space:nowrap}.card-title strong{font-size:4.25mm;line-height:1;letter-spacing:.01em}.card-title span{display:block;margin-top:1.1mm;font-size:1.3mm;line-height:1;font-weight:700;letter-spacing:.08em}.footer-brand{display:none}
    </style>
</head>
<body>
@php
    $studentPhoto = $student->photo ? ($isPdf ? public_path('storage/'.$student->photo) : asset('storage/'.$student->photo)) : null;
    $schoolLogo = $school?->school_logo ? ($isPdf ? public_path('storage/'.$school->school_logo) : asset('storage/'.$school->school_logo)) : null;
    $birthDate = $student->birth_date?->translatedFormat('d M Y') ?: '-';
@endphp
@unless($isPdf)<div class="toolbar"><button onclick="window.print()">Cetak Kartu</button><a href="{{route('students.card.pdf',$student)}}">Unduh PDF</a></div>@endunless
<div class="student-card">
    <div class="shape-one"></div><div class="shape-two"></div>
    @if($schoolLogo)<img class="watermark" src="{{$schoolLogo}}" alt="">@endif
    <header class="card-header"><table class="header-table"><tr>
        <td class="header-logo">@if($schoolLogo)<img class="school-logo" src="{{$schoolLogo}}" alt="Logo sekolah">@else<div class="logo-fallback">◆</div>@endif</td>
        <td class="school-info"><strong>{{$school?->school_name ?: 'Sistem Absensi Siswa'}}</strong><small>{{$school?->school_address ?: 'Kartu identitas siswa sekolah'}}</small></td>
        <td class="card-title"><strong>KARTU SISWA</strong><span>NOMOR INDUK SISWA NASIONAL</span></td>
    </tr></table></header>
    <main class="card-content"><table class="content-table"><tr>
        <td class="photo-cell">@if($studentPhoto)<img class="student-photo" src="{{$studentPhoto}}" alt="Foto {{$student->name}}">@else<div class="photo-placeholder">♟</div>@endif</td>
        <td class="data-cell"><table class="student-data"><tr><td>NISN</td><td>:</td><td>{{$student->nisn ?: '-'}}</td></tr><tr><td>Nama</td><td>:</td><td>{{$student->name}}</td></tr><tr><td>Tempat Lahir</td><td>:</td><td>{{$student->birth_place ?: '-'}}</td></tr><tr><td>Tanggal Lahir</td><td>:</td><td>{{$birthDate}}</td></tr><tr><td>Jenis Kelamin</td><td>:</td><td>{{$student->gender === 'L' ? 'Laki-laki' : 'Perempuan'}}</td></tr></table></td>
        <td class="qr-cell"><div class="qr-box">{!! app(\App\Services\StudentBarcodeService::class)->svg($student,100) !!}</div><div class="qr-caption">SCAN ABSENSI</div></td>
    </tr></table></main>
</div>
</body>
</html>
