<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    public function updateClock(Request $request, $employeeId)
    {
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

            return response()->json(new EmployeeResource($shift->employee), 200);
        }

        $employee = Employee::find($employeeId);

        $employee->attendances()->create([
            'shift_date' => now(),
            'start_at'   => now(),
            'selfie'     => $request->file('selfie'),
        ]);

        return response()->json(new EmployeeResource($employee), 201);
    }
}
