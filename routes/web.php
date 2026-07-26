<?php
use App\Http\Controllers\{AttendanceController,AuthController,DashboardController,HolidayController,MasterController,ReportController,StudentCardController,StudentController};
use Illuminate\Support\Facades\Route;
Route::middleware('guest')->group(function(){
 Route::get('/login',[AuthController::class,'show'])->name('login'); Route::post('/login',[AuthController::class,'login'])->middleware('throttle:6,1')->name('login.store');
});
Route::middleware('auth')->group(function(){
 Route::post('/logout',[AuthController::class,'logout'])->name('logout'); Route::get('/',fn()=>redirect()->route('dashboard')); Route::get('/dashboard',DashboardController::class)->name('dashboard'); Route::get('/account',[AuthController::class,'account'])->name('account.edit'); Route::put('/account',[AuthController::class,'updateAccount'])->name('account.update');
 Route::middleware('role:admin')->group(function(){
  Route::get('/admin/student-search',[AttendanceController::class,'studentSearch'])->name('admin.student-search');
  Route::get('/students/create',[StudentController::class,'create'])->name('students.create'); Route::post('/students',[StudentController::class,'store'])->name('students.store');
  Route::get('/students/{student}/edit',[StudentController::class,'edit'])->whereNumber('student')->name('students.edit'); Route::put('/students/{student}',[StudentController::class,'update'])->whereNumber('student')->name('students.update'); Route::delete('/students/{student}',[StudentController::class,'destroy'])->whereNumber('student')->name('students.destroy'); Route::post('/students/{student}/barcode',[StudentController::class,'regenerate'])->whereNumber('student')->name('students.barcode');
  Route::get('/admin/teachers',[MasterController::class,'teachers'])->name('teachers.index'); Route::post('/admin/teachers/{teacher?}',[MasterController::class,'teacherSave'])->name('teachers.save'); Route::delete('/admin/teachers/{teacher}',[MasterController::class,'teacherDelete'])->name('teachers.delete');
  Route::get('/admin/classes',[MasterController::class,'classes'])->name('classes.index'); Route::post('/admin/classes/{schoolClass?}',[MasterController::class,'classSave'])->name('classes.save'); Route::delete('/admin/classes/{schoolClass}',[MasterController::class,'classDelete'])->name('classes.delete');
  Route::get('/admin/promotions',[MasterController::class,'promotion'])->name('promotions.index'); Route::post('/admin/promotions',[MasterController::class,'promotionSave'])->name('promotions.store');
  Route::get('/admin/users',[MasterController::class,'users'])->name('users.index'); Route::post('/admin/users/{user?}',[MasterController::class,'userSave'])->name('users.save'); Route::get('/admin/settings',[MasterController::class,'settings'])->name('settings.index'); Route::post('/admin/settings',[MasterController::class,'settingSave'])->name('settings.save');
  Route::get('/admin/holidays',[HolidayController::class,'index'])->name('holidays.index'); Route::post('/admin/holidays',[HolidayController::class,'store'])->name('holidays.store'); Route::post('/admin/holidays/sync',[HolidayController::class,'sync'])->name('holidays.sync'); Route::delete('/admin/holidays/{holiday}',[HolidayController::class,'destroy'])->name('holidays.destroy');
 });
 Route::middleware('role:admin,wali_kelas')->group(function(){
  Route::get('/students',[StudentController::class,'index'])->name('students.index'); Route::get('/students/{student}',[StudentController::class,'show'])->whereNumber('student')->name('students.show'); Route::get('/students/{student}/card',[StudentCardController::class,'show'])->whereNumber('student')->name('students.card'); Route::get('/students/{student}/card/pdf',[StudentCardController::class,'pdf'])->whereNumber('student')->name('students.card.pdf');
  Route::get('/attendance/scan',[AttendanceController::class,'scanPage'])->name('attendance.scan'); Route::post('/attendance/scan',[AttendanceController::class,'scan'])->middleware('throttle:30,1')->name('attendance.scan.store'); Route::get('/attendance/manual',[AttendanceController::class,'manual'])->name('attendance.manual'); Route::post('/attendance/manual',[AttendanceController::class,'storeManual'])->name('attendance.manual.store');
 });
 Route::middleware('role:admin,kepala_sekolah,wali_kelas')->group(function(){Route::get('/reports',[ReportController::class,'index'])->name('reports.index');Route::get('/reports/excel',[ReportController::class,'excel'])->name('reports.excel');Route::get('/reports/pdf',[ReportController::class,'pdf'])->name('reports.pdf');});
});
