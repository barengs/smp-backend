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
use App\Http\Controllers\Api\Main\ControlPanelController;
use App\Http\Controllers\Api\Main\TransactionTypeController;
use App\Http\Controllers\Api\Main\AccountController;
use App\Http\Controllers\Api\Main\ChartOfAccountController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
});

Route::apiResource('registration', RegistrationController::class);
Route::get('registration/current-year', [RegistrationController::class, 'getByCurrentYear'])->name('registration.current-year');
Route::post('registration/transaction', [RegistrationController::class, 'createRegistrationTransaction'])->name('registration.transaction');
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
Route::get('dashboard/student-statistics-by-period', [DashboardController::class, 'studentStatisticsByPeriod'])->name('dashboard.student-statistics-by-period');
Route::apiResource('activity', ActivityController::class);
Route::apiResource('news', NewsController::class);
Route::apiResource('supervisor', InternshipSupervisorController::class);
Route::apiResource('control-panel', ControlPanelController::class);
Route::post('control-panel/{id}/update-by-column', [ControlPanelController::class, 'updateByColumn'])->name('control-panel.update-by-column');

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
Route::apiResource('transaction-type', TransactionTypeController::class);
Route::post('transaction-type/{id}/toggle-active', [TransactionTypeController::class, 'toggleActiveStatus'])->name('transaction-type.toggle-active');
Route::apiResource('account', AccountController::class);
Route::post('account/{id}/status', [AccountController::class, 'updateStatus'])->name('account.status');

Route::apiResource('chart-of-account', ChartOfAccountController::class);

Route::group(['prefix' => 'master'], function () {
    Route::apiResource('study', StudyController::class);
    Route::post('study/import', [StudyController::class, 'import'])->name('study.import');
    Route::get('study/import/template', [StudyController::class, 'getImportTemplate'])->name('study.import.template');
    Route::apiResource('profession', ProfessionController::class);
    Route::apiResource('jobdes', JobDesController::class);
    Route::apiResource('classroom', ClassroomController::class);
    Route::apiResource('education-class', EducationClassController::class);
    Route::apiResource('education', EducationController::class);
    Route::post('education/import', [EducationController::class, 'import'])->name('education.import');
    Route::get('education/import/template', [EducationController::class, 'getImportTemplate'])->name('education.import.template');
    Route::apiResource('education-type', EducationTypeController::class);
    Route::apiResource('hostel', HostelController::class);
    Route::post('hostel/import', [HostelController::class, 'import'])->name('hostel.import');
    Route::get('hostel/import/template', [HostelController::class, 'getImportTemplate'])->name('hostel.import.template');
    Route::apiResource('program', ProgramController::class);
    Route::apiResource('occupation', OccupationController::class);
    Route::post('occupation/import', [OccupationController::class, 'import'])->name('occupation.import');
    Route::get('occupation/import/template', [OccupationController::class, 'getImportTemplate'])->name('occupation.import.template');
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
    Route::get('city/by-name', [CityController::class, 'getByName'])->name('city.by-name');
    Route::apiResource('province', ProvinceController::class);
});

Route::get('village/nik/{nik}', [VillageController::class, 'villageByNik']);
