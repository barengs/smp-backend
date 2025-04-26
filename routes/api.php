<?php

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Resources\EducationClassResource;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\Main\StudentController;
use App\Http\Controllers\Api\Master\StudyController;
use App\Http\Controllers\Api\Main\EmployeeController;
use App\Http\Controllers\Api\Master\HostelController;
use App\Http\Controllers\Api\Master\JobDesController;
use App\Http\Controllers\Api\Master\ProgramController;
use App\Http\Controllers\Api\Master\ClassroomController;
use App\Http\Controllers\Api\Master\EducationController;
use App\Http\Controllers\Api\Main\RegistrationController;
use App\Http\Controllers\Api\Master\OccupationController;
use App\Http\Controllers\Api\Master\ProfessionController;
use App\Http\Controllers\Api\Main\ParentProfileController;
use App\Http\Controllers\Api\Master\EducationTypeController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::apiResource('registration', RegistrationController::class);
Route::apiResource('employee', EmployeeController::class);
Route::apiResource('parent', ParentProfileController::class);
Route::apiResource('student', StudentController::class);

Route::group(['prefix' => 'master'], function () {
    Route::apiResource('study', StudyController::class);
    Route::apiResource('profession', ProfessionController::class);
    Route::apiResource('jobdes', JobDesController::class);
    Route::apiResource('classroom', ClassroomController::class);
    Route::apiResource('education-class', EducationClassResource::class);
    Route::apiResource('education', EducationController::class);
    Route::apiResource('education-type', EducationTypeController::class);
    Route::apiResource('hostel', HostelController::class);
    Route::apiResource('program', ProgramController::class);
    Route::apiResource('occupation', OccupationController::class);
});

Route::group(['prefix' => 'region'], function () {
    Route::apiResource('village', VillageController::class);
    Route::apiResource('district', DistrictController::class);
    Route::apiResource('city', CityController::class);
    Route::apiResource('province', ProvinceController::class);
});
