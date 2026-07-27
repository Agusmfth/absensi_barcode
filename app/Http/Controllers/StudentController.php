<?php

namespace App\Http\Controllers;

use App\Exports\StudentsImportTemplate;
use App\Http\Requests\StudentRequest;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentBarcodeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('schoolClass')
            ->when($request->user()->isWali(), fn ($query) => $query->where('class_id', $request->user()->class_id))
            ->when($request->class_id, fn ($query, $value) => $query->where('class_id', $value))
            ->when($request->search, fn ($query, $value) => $query->where(fn ($student) => $student
                ->where('name', 'like', "%{$value}%")
                ->orWhere('nis', 'like', "%{$value}%")
                ->orWhere('nisn', 'like', "%{$value}%")));

        return view('students.index', [
            'students' => $query->latest()->paginate(15)->withQueryString(),
            'classes' => SchoolClass::orderBy('class_name')->get(),
        ]);
    }

    public function create()
    {
        return view('students.form', ['student' => new Student, 'classes' => SchoolClass::all()]);
    }

    public function store(StudentRequest $request, StudentBarcodeService $barcode)
    {
        $data = $request->validated();
        $data['barcode_token'] = $barcode->generateToken();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }
        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function import(Request $request, StudentBarcodeService $barcode)
    {
        $request->validate(['student_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);
        $import = new StudentsImport($barcode);

        try {
            Excel::import($import, $request->file('student_file'));
        } catch (\Throwable $error) {
            report($error);

            return back()->with('import_errors', ['File tidak dapat diproses. Pastikan format dan judul kolom sesuai template.']);
        }

        $errors = collect($import->failures())->map(function ($failure) {
            $nis = $failure->values()['nis'] ?? '-';

            return 'Baris '.$failure->row().' (NIS '.$nis.'): '.implode(' ', $failure->errors());
        })->values()->all();

        return back()
            ->with('success', $import->imported.' siswa berhasil diimpor.')
            ->with('import_errors', $errors);
    }

    public function importTemplate()
    {
        return Excel::download(new StudentsImportTemplate, 'template-import-siswa.xlsx');
    }

    public function show(Student $student)
    {
        $this->guard($student);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.form', ['student' => $student, 'classes' => SchoolClass::all()]);
    }

    public function update(StudentRequest $request, Student $student)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }
        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Data siswa diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return back()->with('success', 'Siswa dihapus.');
    }

    public function regenerate(Student $student, StudentBarcodeService $barcode)
    {
        $this->authorizeAdmin();
        $barcode->regenerate($student);

        return back()->with('success', 'Barcode baru dibuat; barcode lama tidak berlaku.');
    }

    private function guard(Student $student): void
    {
        if (auth()->user()->isWali()) {
            abort_unless((int) $student->class_id === (int) auth()->user()->class_id, 403);
        }
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }
}
