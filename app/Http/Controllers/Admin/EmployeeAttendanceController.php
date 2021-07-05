<?php

namespace App\Http\Controllers\Admin;

use App\Events\EmployeeClockedIn;
use App\Events\EmployeeClockedOut;
use App\Models\Attendance;
use App\Models\Employee;

class EmployeeAttendanceController
{
    public function updateClock($employeeId)
    {
        $shift = Attendance::where('shift_date', now()->format('Y-m-d'))
                           ->whereNull('end_at')
                           ->whereHas('employee', function ($query) use ($employeeId) {
                               $query->where('id', $employeeId);
                           })
                           ->first();
        if ($shift) {
            $shift->update([
                'end_at' => now(),
            ]);

            event(new EmployeeClockedOut($shift));

            return back();
        }

        $employee = Employee::find($employeeId);

        $attendance = $employee->attendances()->create([
            'shift_date' => now(),
            'start_at'   => now(),
        ]);

        event(new EmployeeClockedIn($attendance));

        return back();
    }
}
