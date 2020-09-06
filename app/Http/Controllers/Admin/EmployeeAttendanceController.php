<?php

namespace App\Http\Controllers\Admin;

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

            \Alert::success('Berhasil akhiri shift!')->flash();

            return back();
        }

        $employee = Employee::find($employeeId);

        $employee->attendances()->create([
            'shift_date' => now(),
            'start_at'   => now(),
        ]);

        \Alert::success('Berhasil memulai shift!')->flash();

        return back();
    }
}
