@extends('layouts.app')
@section('title', 'Absensi Manual')
@section('heading', 'Absensi Manual')
@section('subheading', 'Catat status kehadiran seluruh siswa dalam satu kelas')

@section('content')
<div class="card manual-filter-card border-0 shadow-sm mb-4">
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
            <div class="col-md-3"><label class="form-label fw-semibold">Tanggal</label><input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" class="form-control" required></div>
            <div class="col-md-auto"><button class="btn btn-primary manual-show-button"><i class="bi bi-people"></i> Tampilkan Belum Absen</button></div>
        </form>
    </div>
</div>

@if ($students->count())
    <form method="post" action="{{ route('attendance.manual.store') }}" class="card content-card manual-attendance-card border-0 shadow-sm">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <div class="card-body border-bottom manual-list-header">
            <div class="manual-list-title"><h5 class="mb-1">Daftar Kehadiran</h5><small class="text-muted"><span id="visibleStudentCount">{{ $students->count() }}</span> siswa siap dicatat</small></div>
            <div class="manual-search"><label for="studentSearch" class="form-label small fw-semibold">Cari Siswa</label><div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input id="studentSearch" type="search" class="form-control" placeholder="Nama atau NIS..." autocomplete="off"></div></div>
            <div class="manual-date"><label class="form-label small fw-semibold">Tanggal Absensi</label><input type="date" name="date" value="{{ $date }}" class="form-control" required></div>
        </div>
        <div class="table-responsive">
            <table class="table student-table align-middle mb-0">
                <thead><tr><th>Siswa</th><th style="width:220px">Status</th><th>Keterangan</th></tr></thead>
                <tbody>
                @foreach ($students as $student)
                    <tr class="student-attendance-row" data-search="{{ Str::lower($student->name.' '.$student->nis) }}">
                        <td><strong>{{ $student->name }}</strong><br><small class="text-muted">NIS {{ $student->nis }}</small></td>
                        <td data-label="Status"><select name="attendances[{{ $student->id }}][status]" class="form-select"><option value="alfa" selected>Alfa</option><option value="hadir">Hadir</option><option value="terlambat">Terlambat</option><option value="izin">Izin</option><option value="sakit">Sakit</option></select></td>
                        <td data-label="Keterangan"><input class="form-control" name="attendances[{{ $student->id }}][notes]" placeholder="Keterangan (opsional)"></td>
                    </tr>
                @endforeach
                    <tr id="studentSearchEmpty" class="d-none"><td colspan="3" class="empty"><i class="bi bi-search d-block fs-4 mb-2"></i>Siswa tidak ditemukan</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 text-end p-3"><button class="btn btn-primary px-4"><i class="bi bi-check2-circle"></i> Simpan Absensi</button></div>
    </form>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('studentSearch');
    if (!search) return;
    const rows = [...document.querySelectorAll('.student-attendance-row')];
    const count = document.getElementById('visibleStudentCount');
    const empty = document.getElementById('studentSearchEmpty');
    search.addEventListener('input', () => {
        const keyword = search.value.trim().toLocaleLowerCase('id');
        let visible = 0;
        rows.forEach(row => {
            const matches = row.dataset.search.includes(keyword);
            row.classList.toggle('d-none', !matches);
            if (matches) visible++;
        });
        count.textContent = visible;
        empty.classList.toggle('d-none', visible !== 0);
    });
});
</script>
@endpush
