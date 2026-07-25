<?php
namespace App\Http\Requests; use Illuminate\Foundation\Http\FormRequest;
class ScanAttendanceRequest extends FormRequest{public function authorize():bool{return in_array($this->user()?->role,['admin','wali_kelas'],true);}public function rules():array{return['token'=>'required|string|max:100'];}}
