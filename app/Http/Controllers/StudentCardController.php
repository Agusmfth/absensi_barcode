<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StudentCardController extends Controller
{
    public function show(Student $student)
    {
        $this->guard($student);

        return view('students.card', [
            'student' => $student->load('schoolClass'),
            'school' => SchoolSetting::first(),
            'isPdf' => false,
        ]);
    }

    public function pdf(Student $student)
    {
        $this->guard($student);

        return Pdf::loadView('students.card', [
            'student' => $student->load('schoolClass'),
            'school' => SchoolSetting::first(),
            'isPdf' => true,
        ])->setPaper([0, 0, 242.65, 153.01])->download('kartu-'.$student->nis.'.pdf');
    }

    public function all(Request $request)
    {
        $students = Student::with('schoolClass')
            ->when($request->class_id, fn ($query, $class) => $query->where('class_id', $class))
            ->when($request->search, fn ($query, $term) => $query->where(fn ($student) => $student
                ->where('name', 'like', "%{$term}%")
                ->orWhere('nis', 'like', "%{$term}%")
                ->orWhere('nisn', 'like', "%{$term}%")))
            ->orderBy('name')->get();

        return Pdf::loadView('students.cards', [
            'students' => $students,
            'school' => SchoolSetting::first(),
        ])->setPaper([0, 0, 595.28, 841.89])->download('kartu-siswa-semua-portrait.pdf');
    }

    private function guard(Student $student): void
    {
        if (auth()->user()->isWali()) {
            abort_unless((int) $student->class_id === (int) auth()->user()->class_id, 403);
        }
    }
}
