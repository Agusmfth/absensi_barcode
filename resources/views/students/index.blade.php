@extends('layouts.app')

@section('title', 'Data Siswa')
@section('heading', 'Data Siswa')
@section('subheading', 'Kelola data siswa, kelas, dan kartu absensi')

@section('actions')
    @if (auth()->user()->isAdmin())
        <button class="btn btn-primary page-action" data-bs-toggle="modal" data-bs-target="#createStudentModal" type="button">
            <i class="bi bi-plus-lg"></i><span>Tambah Siswa</span>
        </button>
    @endif
@endsection

@section('content')
<div class="card content-card border-0 shadow-sm">
    <div class="card-body filter-panel">
        <form class="row g-2 align-items-center">
            <div class="col-lg-5">
                <div class="input-group clean-input">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau NISN">
                </div>
            </div>
            <div class="col-lg-4">
                <select class="form-select" name="class_id">
                    <option value="">Semua kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Terapkan</button>
                <a href="{{ route('students.index') }}" class="btn btn-soft">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table student-table align-middle mb-0">
            <thead><tr><th>Siswa</th><th>NIS / NISN</th><th>Kelas</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($students as $student)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if ($student->photo)
                                <img class="table-avatar" src="{{ Storage::url($student->photo) }}" alt="Foto {{ $student->name }}">
                            @else
                                <div class="table-avatar avatar-initial">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                            @endif
                            <div><div class="fw-semibold student-name">{{ $student->name }}</div><small class="text-muted">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</small></div>
                        </div>
                    </td>
                    <td><span class="fw-medium">{{ $student->nis }}</span><br><small class="text-muted">{{ $student->nisn ?: 'NISN belum diisi' }}</small></td>
                    <td><span class="class-pill">{{ $student->schoolClass->class_name }}</span></td>
                    <td><span class="status-dot {{ $student->is_active ? 'active' : 'inactive' }}"><i></i>{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <div class="table-actions justify-content-end">
                            <a class="icon-action" title="Detail" href="{{ route('students.show', $student) }}"><i class="bi bi-eye"></i></a>
                            <a class="icon-action text-primary" title="Kartu siswa" href="{{ route('students.card', $student) }}"><i class="bi bi-person-vcard"></i></a>
                            @if (auth()->user()->isAdmin())
                                <button class="icon-action text-warning border-0" title="Edit" data-bs-toggle="modal" data-bs-target="#editStudent{{$student->id}}"><i class="bi bi-pencil-square"></i></button>
                                <form method="post" action="{{ route('students.destroy', $student) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="icon-action text-danger border-0" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty"><i class="bi bi-people d-block fs-1 mb-2"></i>Data siswa tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($students->hasPages())
        <div class="card-footer bg-white border-0 px-4 py-3">{{ $students->links() }}</div>
    @endif
</div>
@foreach($students as $student)<div class="modal fade" id="editStudent{{$student->id}}" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content border-0 shadow-lg"><div class="modal-header"><div><h5 class="modal-title">Edit Data Siswa</h5><small class="text-muted">Perbarui informasi {{$student->name}}</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="post" enctype="multipart/form-data" action="{{route('students.update',$student)}}"><div class="modal-body row g-3">@csrf @method('put')<div class="col-md-6"><label class="form-label">NIS</label><input class="form-control" name="nis" value="{{$student->nis}}" required></div><div class="col-md-6"><label class="form-label">NISN</label><input class="form-control" name="nisn" value="{{$student->nisn}}"></div><div class="col-md-6"><label class="form-label">Nama</label><input class="form-control" name="name" value="{{$student->name}}" required></div><div class="col-md-6"><label class="form-label">Jenis Kelamin</label><select class="form-select" name="gender"><option value="L" @selected($student->gender==='L')>Laki-laki</option><option value="P" @selected($student->gender==='P')>Perempuan</option></select></div><div class="col-md-6"><label class="form-label">Kelas</label><select class="form-select" name="class_id" required>@foreach($classes as $class)<option value="{{$class->id}}" @selected($student->class_id===$class->id)>{{$class->class_name}}</option>@endforeach</select></div><div class="col-md-6"><label class="form-label">Foto</label><input type="file" class="form-control" name="photo" accept="image/png,image/jpeg"></div><div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="address" rows="2">{{$student->address}}</textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="bi bi-check2"></i> Simpan Perubahan</button></div></form></div></div></div>@endforeach
<div class="modal fade" id="createStudentModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content border-0 shadow-lg"><div class="modal-header"><div><h5 class="modal-title">Tambah Siswa</h5><small class="text-muted">Lengkapi data siswa baru</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="post" enctype="multipart/form-data" action="{{route('students.store')}}"><div class="modal-body row g-3">@csrf<div class="col-md-6"><label class="form-label">NIS</label><input class="form-control" name="nis" required></div><div class="col-md-6"><label class="form-label">NISN</label><input class="form-control" name="nisn"></div><div class="col-md-6"><label class="form-label">Nama</label><input class="form-control" name="name" required></div><div class="col-md-6"><label class="form-label">Jenis Kelamin</label><select class="form-select" name="gender"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div><div class="col-md-6"><label class="form-label">Kelas</label><select class="form-select" name="class_id" required>@foreach($classes as $class)<option value="{{$class->id}}">{{$class->class_name}}</option>@endforeach</select></div><div class="col-md-6"><label class="form-label">Foto</label><input type="file" class="form-control" name="photo" accept="image/png,image/jpeg"></div><div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="address" rows="2"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Siswa</button></div></form></div></div></div>
@endsection
