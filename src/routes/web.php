<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\VerificationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::get('/', function () {
    return redirect('/login');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])
        ->middleware(['throttle:6,1'])->name('verification.send');
 });   

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store'); 
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/stamp_correction_request', [StampCorrectionRequestController::class, 'store'])->name('stamp_correction_request.store');
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
    ->name('stamp_correction_request.index');
    Route::get('/stamp_correction_request/approve/{id}', [App\Http\Controllers\Admin\StampCorrectionRequestController::class, 'approve'])->name('admin.stamp_correction_request.approve');
    Route::get('/staff/list', [StaffController::class, 'index'])->name('admin.staff.list');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    
    // 管理者用の勤怠一覧画面
    Route::get('/attendance/list', [AdminAttendanceController::class, 'list'])->name('admin.attendance.list');
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::patch('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
    Route::get('/stamp_correction_request/list', [App\Http\Controllers\Admin\StampCorrectionRequestController::class, 'index'])
        ->name('admin.stamp_correction_request.list');
    Route::get('/stamp_correction_request/show/{id}', [App\Http\Controllers\Admin\StampCorrectionRequestController::class, 'show'])
        ->name('admin.stamp_correction_request.show');
    Route::get('/stamp_correction_request/approve/{id}', [App\Http\Controllers\Admin\StampCorrectionRequestController::class, 'approve'])
        ->name('admin.stamp_correction_request.approve');
    Route::get('/staff/list', [App\Http\Controllers\Admin\StaffController::class, 'index'])->name('admin.staff.list');
    Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'staffList'])->name('admin.attendance.staff_list');
    Route::get('/attendance/staff/{id}/csv', [AdminAttendanceController::class, 'exportCsv'])->name('admin.attendance.csv_export');
});

