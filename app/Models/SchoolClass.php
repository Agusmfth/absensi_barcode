<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
class SchoolClass extends Model{use HasFactory;protected $guarded=[];protected $casts=['is_active'=>'boolean'];public function teacher(){return $this->belongsTo(Teacher::class);}public function students(){return $this->hasMany(Student::class,'class_id');}public function users(){return $this->hasMany(User::class,'class_id');}}
