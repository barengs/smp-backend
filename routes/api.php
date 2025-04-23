<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\Master\ClassroomController;
use App\Http\Controllers\Api\Master\EducationController;
use App\Http\Controllers\Api\Master\EducationTypeController;
use App\Http\Controllers\Api\Master\JobDesController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\Master\StudyController;
use App\Http\Controllers\Api\Master\ProfessionController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::apiResource('employee', EmployeeController::class);

Route::group(['prefix' => 'master'], function () {
    Route::apiResource('study', StudyController::class);
    Route::apiResource('profession', ProfessionController::class);
    Route::apiResource('jobdes', JobDesController::class);
    Route::apiResource('classroom', ClassroomController::class);
    Route::apiResource('education', EducationController::class);
    Route::apiResource('education-type', EducationTypeController::class);
});

Route::group(['prefix' => 'region'], function () {
    Route::apiResource('village', VillageController::class);
    Route::apiResource('district', DistrictController::class);
    Route::apiResource('city', CityController::class);
    Route::apiResource('province', ProvinceController::class);
});
