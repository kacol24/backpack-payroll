<?php

namespace App\Http\Controllers\Admin;

use App\Events\EmployeeClockedIn;
use App\Events\EmployeeClockedOut;
use App\Models\Employee;

class EmployeeAttendanceController
{
    public function updateClock($employeeId)
    {
        $employee = Employee::find($employeeId);

        if ($employee->isOnShift()) {
            event(new EmployeeClockedOut($employee->clockOut()));

            return back();
        }

        event(new EmployeeClockedIn($employee->clockIn()));

        return back();
    }
}
