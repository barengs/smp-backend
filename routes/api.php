<?php

use App\Http\Controllers\Api\Main\TransactionController;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Resources\EducationClassResource;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\Master\MenuController;
use App\Http\Controllers\Api\Master\NewsController;
use App\Http\Controllers\Api\Master\RoleController;
use App\Http\Controllers\Api\Main\ProductController;
use App\Http\Controllers\Api\Main\StudentController;
use App\Http\Controllers\Api\Master\StudyController;
use App\Http\Controllers\Api\Main\ActivityController;
use App\Http\Controllers\Api\Main\EmployeeController;
use App\Http\Controllers\Api\Master\HostelController;
use App\Http\Controllers\Api\Master\JobDesController;
use App\Http\Controllers\Api\Main\DashboardController;
use App\Http\Controllers\Api\Master\ProgramController;
use App\Http\Controllers\Api\Master\ClassroomController;
use App\Http\Controllers\Api\Master\EducationController;
use App\Http\Controllers\Api\Main\RegistrationController;
use App\Http\Controllers\Api\Master\ClassGroupController;
use App\Http\Controllers\Api\Master\OccupationController;
use App\Http\Controllers\Api\Master\PermissionController;
use App\Http\Controllers\Api\Master\ProfessionController;
use App\Http\Controllers\Api\Main\ParentProfileController;
use App\Http\Controllers\Api\Master\AcademicYearController;
use App\Http\Controllers\Api\Master\EducationTypeController;
use App\Http\Controllers\Api\Master\EducationClassController;
use App\Http\Controllers\Api\Main\InternshipSupervisorController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
});

Route::apiResource('registration', RegistrationController::class);
Route::apiResource('employee', EmployeeController::class);
Route::get('employee/export', [EmployeeController::class, 'export'])->name('employee.export');
Route::post('employee/import', [EmployeeController::class, 'import'])->name('employee.import');
Route::get('employee/import/template', [EmployeeController::class, 'getImportTemplate'])->name('employee.import.template');
Route::apiResource('parent', ParentProfileController::class);
Route::get('parent/nik/{nik}/cek', [ParentProfileController::class, 'getByNik'])
    ->name('parent.getByNik');
Route::apiResource('student', StudentController::class);
Route::post('student-status', [StudentController::class, 'setStatus'])->name('student.status');
Route::get('student/program/{programId}', [StudentController::class, 'getStudentByProgramId'])
    ->name('student.program');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::apiResource('activity', ActivityController::class);
Route::apiResource('news', NewsController::class);
Route::apiResource('supervisor', InternshipSupervisorController::class);

/* fitur perbaikan */
Route::apiResource('product', ProductController::class);
Route::apiResource('transaction', TransactionController::class);
Route::post('transaction/deposit', [TransactionController::class, 'cashDeposit'])->name('transaction.deposit');
Route::post('transaction/withdraw', [TransactionController::class, 'cashWithdraw'])->name('transaction.withdraw');
Route::post('transaction/fund-transfer', [TransactionController::class, 'fundTransfer'])->name('transaction.fund-transfer');
Route::get('transaction/get-by-account', [TransactionController::class, 'getByAccount'])->name('transaction.get-by-account');
Route::get('transaction/get-by-status', [TransactionController::class, 'getByStatus'])->name('transaction.get-by-status');
Route::get('transaction/get-by-date', [TransactionController::class, 'getByDateRange'])->name('transaction.get-by-date');
Route::post('transaction/reverse-transaction', [TransactionController::class, 'reverseTransaction'])->name('transaction.reverse-transaction');
Route::get('transaction/report-transaction', [TransactionController::class, 'getTransactionSummary'])->name('transaction.report-transaction');

Route::group(['prefix' => 'master'], function () {
    Route::apiResource('study', StudyController::class);
    Route::apiResource('profession', ProfessionController::class);
    Route::apiResource('jobdes', JobDesController::class);
    Route::apiResource('classroom', ClassroomController::class);
    Route::apiResource('education-class', EducationClassController::class);
    Route::apiResource('education', EducationController::class);
    Route::apiResource('education-type', EducationTypeController::class);
    Route::apiResource('hostel', HostelController::class);
    Route::apiResource('program', ProgramController::class);
    Route::apiResource('occupation', OccupationController::class);
    Route::apiResource('role', RoleController::class);
    Route::apiResource('permission', PermissionController::class);
    Route::apiResource('menu', MenuController::class);
    Route::apiResource('class-group', ClassGroupController::class);
    Route::apiResource('academic-year', AcademicYearController::class);
});

Route::group(['prefix' => 'region'], function () {
    Route::apiResource('village', VillageController::class);
    Route::apiResource('district', DistrictController::class);
    Route::apiResource('city', CityController::class);
    Route::apiResource('province', ProvinceController::class);
});

Route::get('village/nik/{nik}', [VillageController::class, 'villageByNik']);
