<?php
namespace App\Http\Controllers; use App\Models\Student; use Barryvdh\DomPDF\Facade\Pdf;
class StudentCardController extends Controller{public function show(Student $student){$this->guard($student);return view('students.card',compact('student'));}public function pdf(Student $student){$this->guard($student);return Pdf::loadView('students.card',compact('student'))->setPaper([0,0,242.65,153.07])->download('kartu-'.$student->nis.'.pdf');}private function guard(Student $s){if(auth()->user()->isWali())abort_unless((int)$s->class_id===(int)auth()->user()->class_id,403);}}
