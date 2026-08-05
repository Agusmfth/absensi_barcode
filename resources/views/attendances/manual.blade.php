@extends('layouts.app')
@section('title', 'Absensi Manual')
@section('heading', 'Absensi Manual')
@section('subheading', 'Catat status kehadiran seluruh siswa dalam satu kelas')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="get">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Pilih Kelas</label>
                @if (auth()->user()->isWali())
                    <input class="form-control" value="{{ auth()->user()->schoolClass->class_name }}" disabled>
                    <input type="hidden" name="class_id" value="{{ auth()->user()->class_id }}">
                @else
                    <select name="class_id" class="form-select" required>
                        <option value="">Pilih kelas terlebih dahulu</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="col-md-3"><label class="form-label fw-semibold">Tanggal</label><input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" class="form-control" required></div><div class="col-md-auto"><button class="btn btn-primary"><i class="bi bi-people"></i> Tampilkan Belum Absen</button></div>
        </form>
    </div>
</div>

@if ($students->count())
    <form method="post" action="{{ route('attendance.manual.store') }}" class="card content-card manual-attendance-card border-0 shadow-sm">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <div class="card-body border-bottom d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div><h5 class="mb-1">Daftar Kehadiran</h5><small class="text-muted">{{ $students->count() }} siswa siap dicatat</small></div>
            <div><label class="form-label small fw-semibold">Tanggal Absensi</label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
        </div>
        <div class="table-responsive">
            <table class="table student-table align-middle mb-0">
                <thead><tr><th>Siswa</th><th style="width:220px">Status</th><th>Keterangan</th></tr></thead>
                <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td><strong>{{ $student->name }}</strong><br><small class="text-muted">NIS {{ $student->nis }}</small></td>
                        <td data-label="Status"><select name="attendances[{{ $student->id }}][status]" class="form-select"><option value="alfa" selected>Alfa</option><option value="hadir">Hadir</option><option value="terlambat">Terlambat</option><option value="izin">Izin</option><option value="sakit">Sakit</option></select></td>
                        <td data-label="Keterangan"><input class="form-control" name="attendances[{{ $student->id }}][notes]" placeholder="Keterangan (opsional)"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 text-end p-3"><button class="btn btn-primary px-4"><i class="bi bi-check2-circle"></i> Simpan Absensi</button></div>
    </form>
@endif
@endsection

