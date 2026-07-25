<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class Holiday extends Model { protected $guarded=[]; protected $casts=['holiday_date'=>'date','is_active'=>'boolean']; }
