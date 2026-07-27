<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    public function promotion()
    {
        return view('master.promotion', [
            'students' => Student::with('schoolClass')->where('is_active', true)->orderBy('class_id')->orderBy('name')->get(),
            'classes' => SchoolClass::where('is_active', true)->orderBy('grade_level')->orderBy('class_name')->get(),
        ]);
    }

    public function promotionSave(Request $request)
    {
        $data = $request->validate(['decisions' => 'required|array', 'decisions.*' => 'required|string']);
        $students = Student::whereIn('id', array_keys($data['decisions']))->get();
        DB::transaction(function () use ($students, $data) {
            foreach ($students as $student) {
                $decision = $data['decisions'][$student->id];
                if ($decision === 'graduate') {
                    $student->update(['is_active' => false]);

                    continue;
                }
                if ($decision === 'stay') {
                    $student->update(['is_active' => true]);

                    continue;
                }
                abort_unless(ctype_digit($decision) && SchoolClass::whereKey($decision)->exists(), 422, 'Kelas tujuan tidak valid.');
                $student->update(['class_id' => (int) $decision, 'is_active' => true]);
            }
        });

        return back()->with('success', 'Proses kenaikan kelas berhasil disimpan.');
    }

    public function teachers(Request $request)
    {
        $items = Teacher::with('schoolClass')
            ->when($request->search, fn ($query, $value) => $query->where('name', 'like', "%{$value}%"))
            ->paginate(15);

        return view('master.index', ['type' => 'teachers', 'items' => $items]);
    }

    public function teacherSave(Request $request, Teacher $teacher)
    {
        $data = $request->validate(['name' => 'required', 'nip' => ['nullable', Rule::unique('teachers')->ignore($teacher)], 'gender' => 'required|in:L,P', 'phone' => 'nullable', 'email' => 'nullable|email', 'address' => 'nullable']);
        $teacher->exists ? $teacher->update($data) : Teacher::create($data);

        return back()->with('success', 'Data guru disimpan.');
    }

    public function teacherDelete(Teacher $teacher)
    {
        abort_if($teacher->schoolClass()->exists(), 422, 'Guru masih menjadi wali kelas.');
        $teacher->delete();

        return back()->with('success', 'Guru dihapus.');
    }

    public function classes()
    {
        return view('master.index', ['type' => 'classes', 'items' => SchoolClass::with('teacher')->withCount('students')->paginate(15), 'teachers' => Teacher::whereDoesntHave('schoolClass')->orWhereHas('schoolClass')->get()]);
    }

    public function classSave(Request $request, SchoolClass $schoolClass)
    {
        $data = $request->validate(['class_name' => 'required', 'grade_level' => 'required|integer|min:1|max:12', 'major' => 'nullable', 'teacher_id' => ['nullable', 'exists:teachers,id', Rule::unique('school_classes')->ignore($schoolClass)], 'academic_year' => 'required', 'is_active' => 'nullable|boolean']);
        $schoolClass->exists ? $schoolClass->update($data) : SchoolClass::create($data);

        return back()->with('success', 'Kelas disimpan.');
    }

    public function classDelete(SchoolClass $schoolClass)
    {
        abort_if($schoolClass->students()->exists(), 422, 'Kelas masih memiliki siswa.');
        $schoolClass->delete();

        return back()->with('success', 'Kelas dihapus.');
    }

    public function users()
    {
        return view('master.index', ['type' => 'users', 'items' => User::with('schoolClass')->paginate(15), 'classes' => SchoolClass::all()]);
    }

    public function userSave(Request $request, User $user)
    {
        $data = $request->validate(['name' => 'required', 'email' => ['required', 'email', Rule::unique('users')->ignore($user)], 'username' => ['required', Rule::unique('users')->ignore($user)], 'role' => 'required|in:admin,kepala_sekolah,wali_kelas', 'class_id' => 'nullable|required_if:role,wali_kelas|exists:school_classes,id', 'password' => $user->exists ? 'nullable|min:8' : 'required|min:8', 'is_active' => 'nullable|boolean']);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['class_id'] = $data['role'] === 'wali_kelas' ? $data['class_id'] : null;
        $user->exists ? $user->update($data) : User::create($data);

        return back()->with('success', 'Pengguna disimpan.');
    }

    public function settings()
    {
        return view('settings', ['setting' => SchoolSetting::firstOrNew()]);
    }

    public function settingSave(Request $request)
    {
        $data = $request->validate([
            'school_name' => 'required', 'school_address' => 'nullable', 'school_phone' => 'nullable',
            'school_email' => 'nullable|email', 'principal_name' => 'nullable', 'academic_year' => 'required',
            'semester' => 'required|in:ganjil,genap', 'attendance_start_time' => 'required',
            'late_time_limit' => 'required', 'school_logo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('school_logo')) {
            $data['school_logo'] = $request->file('school_logo')->store('school', 'public');
        }
        $data['timezone'] = 'Asia/Jakarta';
        SchoolSetting::updateOrCreate(['id' => SchoolSetting::value('id') ?: 1], $data);

        return back()->with('success', 'Pengaturan sekolah berhasil diperbarui.');
    }
}
