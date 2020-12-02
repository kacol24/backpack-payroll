<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

use App\Http\Controllers\Admin\EmployeeAttendanceController;

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('employee', 'EmployeeCrudController');
    Route::crud('allowance', 'AllowanceCrudController');
    Route::crud('deduction', 'DeductionCrudController');
    Route::crud('payslip', 'PayslipCrudController');
    Route::crud('attendance', 'AttendanceCrudController');
    Route::get('employee/{id}/clock-in', [EmployeeAttendanceController::class, 'updateClock'])->name('employee.clock_in');
    Route::get('employee/{id}/clock-out', [EmployeeAttendanceController::class, 'updateClock'])->name('employee.clock_out');

    Route::view('attendance-calendar', 'reports.attendance.calendar')->name('report.calendar');
}); // this should be the absolute last line of this file
