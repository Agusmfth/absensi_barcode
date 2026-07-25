<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\SoftDeletes;
class Student extends Model{use HasFactory,SoftDeletes;protected $guarded=['id'];protected $casts=['birth_date'=>'date','is_active'=>'boolean'];public function schoolClass(){return $this->belongsTo(SchoolClass::class,'class_id');}public function attendances(){return $this->hasMany(Attendance::class);}}
