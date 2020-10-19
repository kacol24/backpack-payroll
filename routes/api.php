<?php

use App\Http\Controllers\Api\EmployeeAttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')
     ->name('api.')
     ->group(function () {
         Route::apiResource('employees', 'EmployeeController');
         Route::post('employee/{id}/clock-in', [EmployeeAttendanceController::class, 'updateClock'])
              ->name('employee.clock_in');
         Route::post('employee/{id}/clock-out', [EmployeeAttendanceController::class, 'updateClock'])
              ->name('employee.clock_out');

         Route::apiResource('attendances', 'AttendanceController');
     });
