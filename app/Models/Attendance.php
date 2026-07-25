<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
class Attendance extends Model{use HasFactory;protected $guarded=['id'];protected $casts=['attendance_date'=>'date'];public function student(){return $this->belongsTo(Student::class);}public function recorder(){return $this->belongsTo(User::class,'recorded_by');}}
