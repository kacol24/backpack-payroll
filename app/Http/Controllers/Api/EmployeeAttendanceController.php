<?php

namespace App\Http\Controllers\Api;

use App\Events\EmployeeClockedIn;
use App\Events\EmployeeClockedOut;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    public function updateClock(Request $request, $employeeId)
    {
        $employee = Employee::find($employeeId);
        if ($employee->isOnShift()) {
            $attendance = $employee->clockOut();
            $attendance->selfie_out = $request->file('selfie_out');
            $attendance->save();

            event(new EmployeeClockedOut($attendance, $pushbullet = true));

            return response()->json(new EmployeeResource($employee), 200);
        }

        $attendance = $employee->clockIn();
        $attendance->selfie_in = $request->file('selfie_in');
        $attendance->save();

        event(new EmployeeClockedIn($attendance, $pushbullet = true));

        return response()->json(new EmployeeResource($employee), 201);
    }
}
