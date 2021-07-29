<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class Employee extends Model
{
    use CrudTrait;
    use RevisionableTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'employees';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = [
        'start_date',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'allowances' => 'object',
        'deductions' => 'object',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function identifiableName()
    {
        return "[{$this->employee_number}] " . $this->name;
    }

    public function attendanceButtons()
    {
        $shift = $this->isOnShift();

        if ($this->is_active) {
            if ($shift) {
                return '<a class="btn btn-sm btn-link" href="'.route('employee.clock_out', $this->id).'">
                            <i class="la la-stop-circle"></i> Clock Out
                        </a>';
            }

            return '<a class="btn btn-sm btn-link" href="'.route('employee.clock_in', $this->id).'">
                        <i class="la la-play-circle"></i> Clock In
                    </a>';
        }

    public function isOnShift()
    {
        return $this->attendances->where('shift_date', today())
                                 ->whereNull('end_at')
                                 ->sortByDesc('start_at')
                                 ->first();
    }

    public function clockIn()
    {
        return $this->attendances()->create([
            'shift_date' => now(),
            'start_at'   => now(),
        ]);
    }

    public function clockOut()
    {
        $attendance = $this->isOnShift();

        $attendance->update([
            'end_at'       => now(),
            'hours_worked' => calculate_delta_hours($attendance->start_at, now())
        ]);

        return $attendance;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function shiftOnDate($date = false)
    {
        if (! $date) {
            $date = now()->format('Y-m-d');
        }

        return $this->attendances()
                    ->whereDate('start_at', $date)
                    ->orderBy('start_at', 'desc')
                    ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getTotalAllowancesAttribute()
    {
        if (! $this->allowances) {
            return 0;
        }

        $totalAllowances = json_decode(json_encode($this->allowances), true);

        return array_reduce($totalAllowances, function ($carry, $allowance) {
            $carry += strip_money_mask($allowance['amount']);

            return $carry;
        }, $initial = 0);
    }

    public function getTotalDeductionsAttribute()
    {
        if (! $this->deductions) {
            return 0;
        }

        $totalDeductions = json_decode(json_encode($this->deductions), true);

        return array_reduce($totalDeductions, function ($carry, $deduction) {
            $carry += strip_money_mask($deduction['amount']);

            return $carry;
        }, $initial = 0);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setSalaryAttribute($value)
    {
        $this->attributes['salary'] = strip_money_mask($value);
    }
}
