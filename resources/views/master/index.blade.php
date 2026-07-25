@extends('layouts.app')

@section('title', $type === 'teachers' ? 'Data Guru' : ($type === 'classes' ? 'Data Kelas' : 'Pengguna'))
@section('heading', $type === 'teachers' ? 'Data Guru' : ($type === 'classes' ? 'Data Kelas' : 'Pengguna'))
@section('subheading', $type === 'teachers' ? 'Kelola guru dan wali kelas sekolah' : ($type === 'classes' ? 'Kelola struktur kelas dan wali kelas' : 'Kelola akun dan hak akses pengguna'))

@section('content')
<div class="master-page">
<div class="master-toolbar"><div class="toolbar-intro"><div class="toolbar-icon"><i class="bi {{ $type === 'teachers' ? 'bi-person-badge' : ($type === 'classes' ? 'bi-building' : 'bi-people') }}"></i></div><div><strong>{{ $items->total() }} {{ $type === 'teachers' ? 'guru' : ($type === 'classes' ? 'kelas' : 'pengguna') }}</strong><small>Data aktif dalam sistem</small></div></div><div class="toolbar-search"><i class="bi bi-search"></i><input placeholder="Cari data..." oninput="filterMaster(this.value)"></div></div>
<div class="row g-4">
    <div class="col-xl-4">
        <div class="card master-form-card border-0 shadow-sm">
            <div class="card-header"><div class="form-card-icon"><i class="bi bi-plus-lg"></i></div><div><strong>Tambah {{ ucfirst($type) }}</strong><small>Masukkan data baru</small></div></div>
            <div class="card-body">
                <form method="post" action="{{ $type === 'teachers' ? route('teachers.save') : ($type === 'classes' ? route('classes.save') : route('users.save')) }}">
                    @csrf

                    @if ($type === 'teachers')
                        <input class="form-control mb-2" name="name" placeholder="Nama guru" required>
                        <input class="form-control mb-2" name="nip" placeholder="NIP">
                        <select class="form-select mb-2" name="gender">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <input class="form-control mb-2" name="phone" placeholder="Telepon">
                        <input class="form-control mb-2" type="email" name="email" placeholder="Email">
                    @elseif ($type === 'classes')
                        <input class="form-control mb-2" name="class_name" placeholder="Nama kelas" required>
                        <input class="form-control mb-2" type="number" name="grade_level" placeholder="Tingkat" required>
                        <input class="form-control mb-2" name="major" placeholder="Jurusan (opsional)">
                        <input class="form-control mb-2" name="academic_year" value="{{ date('Y').'/'.(date('Y') + 1) }}" required>
                        <select class="form-select mb-2" name="teacher_id">
                            <option value="">Tanpa wali kelas</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input class="form-control mb-2" name="name" placeholder="Nama" required>
                        <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
                        <input class="form-control mb-2" name="username" placeholder="Username" required>
                        <input class="form-control mb-2" type="password" name="password" placeholder="Password minimal 8 karakter" required>
                        <select class="form-select mb-2" name="role">
                            <option value="admin">Admin</option>
                            <option value="kepala_sekolah">Kepala Sekolah</option>
                            <option value="wali_kelas">Wali Kelas</option>
                        </select>
                        <select class="form-select mb-2" name="class_id">
                            <option value="">Pilih kelas (untuk wali kelas)</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <button class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table master-table align-middle mb-0">
                    <thead><tr><th>Nama</th><th>Informasi</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($items as $item)
                        <tr class="master-row">
                            <td><div class="d-flex align-items-center gap-3">@if($type==='classes')<div class="table-avatar avatar-initial"><i class="bi bi-building"></i></div>@else<div class="table-avatar avatar-initial">{{strtoupper(substr($item->name,0,1))}}</div>@endif<div><strong class="d-block">{{ $type === 'classes' ? $item->class_name : $item->name }}</strong><small class="text-muted">{{$type==='classes'?'Tingkat '.$item->grade_level:($type==='teachers'?'Tenaga pendidik':ucwords(str_replace('_',' ',$item->role)))}}</small></div></div></td>
                            <td>
                                @if ($type === 'teachers')
                                    {{ $item->nip ?: '-' }} · Wali: {{ $item->schoolClass?->class_name ?: '-' }}
                                @elseif ($type === 'classes')
                                    {{ $item->students_count }} siswa · {{ $item->teacher?->name ?: 'Belum ada wali' }}
                                @else
                                    {{ $item->email }}<br>
                                    <small>{{ $item->role }} · {{ $item->schoolClass?->class_name }}</small>
                                @endif
                            </td>
                            <td><span class="status-dot {{ ($item->is_active ?? true) ? 'active' : 'inactive' }}"><i></i>{{ ($item->is_active ?? true) ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                <div class="row-actions"><button class="icon-action text-primary" title="Edit" data-bs-toggle="modal" data-bs-target="#edit{{ $type }}{{ $item->id }}"><i class="bi bi-pencil-square"></i></button>
                                @if ($type !== 'users')
                                    <form method="post" action="{{ $type === 'teachers' ? route('teachers.delete', $item) : route('classes.delete', $item) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="icon-action text-danger border-0" title="Hapus"><i class="bi bi-trash3"></i></button>
                                    </form>
                                @else
                                    <small>Login: {{ $item->last_login_at?->format('d/m/Y H:i') ?: '-' }}</small>
                                @endif</div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($items->hasPages())<div class="card-footer bg-white border-0">{{ $items->links() }}</div>@endif
        </div>
    </div>
</div>
<script>function filterMaster(value){value=value.toLowerCase();document.querySelectorAll('.master-row').forEach(row=>row.style.display=row.innerText.toLowerCase().includes(value)?'':'none')}</script>
@foreach ($items as $item)
<div class="modal fade" id="edit{{ $type }}{{ $item->id }}" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg"><div class="modal-header"><div><h5 class="modal-title mb-1">Edit Data</h5><small class="text-muted">Perbarui informasi tanpa meninggalkan halaman</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="post" action="{{ $type === 'teachers' ? route('teachers.save', $item) : ($type === 'classes' ? route('classes.save', $item) : route('users.save', $item)) }}"><div class="modal-body">@csrf
@if($type==='teachers')<label class="form-label">Nama</label><input class="form-control mb-3" name="name" value="{{$item->name}}" required><label class="form-label">NIP</label><input class="form-control mb-3" name="nip" value="{{$item->nip}}"><label class="form-label">Jenis Kelamin</label><select class="form-select" name="gender"><option value="L" @selected($item->gender==='L')>Laki-laki</option><option value="P" @selected($item->gender==='P')>Perempuan</option></select>@elseif($type==='classes')<label class="form-label">Nama Kelas</label><input class="form-control mb-3" name="class_name" value="{{$item->class_name}}" required><label class="form-label">Tingkat</label><input class="form-control mb-3" type="number" name="grade_level" value="{{$item->grade_level}}" required><label class="form-label">Tahun Ajaran</label><input class="form-control" name="academic_year" value="{{$item->academic_year}}" required>@else<label class="form-label">Nama</label><input class="form-control mb-3" name="name" value="{{$item->name}}" required><label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" value="{{$item->email}}" required><label class="form-label">Username</label><input class="form-control" name="username" value="{{$item->username}}" required><input type="hidden" name="role" value="{{$item->role}}"><input type="hidden" name="class_id" value="{{$item->class_id}}">@endif</div><div class="modal-footer"><button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="bi bi-check2"></i> Simpan Perubahan</button></div></form></div></div></div>
@endforeach
@endsection
