<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Attendance;
use App\Models\Employee;
use App\Notifications\EmployeeAttendance;
use App\User;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    public function updateClock(Request $request, $employeeId)
    {
        $super = User::find(1);

        $shift = Attendance::where('shift_date', now()->format('Y-m-d'))
                           ->latest()
                           ->whereNull('end_at')
                           ->whereHas('employee', function ($query) use ($employeeId) {
                               $query->where('id', $employeeId);
                           })
                           ->first();
        if ($shift) {
            $shift->update([
                'end_at' => now(),
            ]);
            $shift->selfie_out = $request->file('selfie_out');
            $shift->save();

            $super->notify(new EmployeeAttendance($shift, Attendance::TYPE_CLOCK_OUT));

            return response()->json(new EmployeeResource($shift->employee), 200);
        }

        $employee = Employee::find($employeeId);

        $attendance = $employee->attendances()->create([
            'shift_date' => now(),
            'start_at'   => now(),
        ]);
        $attendance->selfie_in = $request->file('selfie_in');
        $attendance->save();

        $super->notify(new EmployeeAttendance($attendance, Attendance::TYPE_CLOCK_IN));

        return response()->json(new EmployeeResource($employee), 201);
    }
}
