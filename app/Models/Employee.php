<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use CrudTrait;
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
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function attendanceButtons()
    {
        $shift = $this->isOnShift();

        if ($shift) {
            return '<a class="btn btn-sm btn-link" href="' . route('employee.clock_out', $this->id) . '"><i class="la la-stop-circle"></i> Clock Out</a>';
        }

        return '<a class="btn btn-sm btn-link" href="' . route('employee.clock_in', $this->id) . '"><i class="la la-play-circle"></i> Clock In</a>';
    }

    public function isOnShift()
    {
        $employee = $this;

        return Attendance::where('shift_date', now()->format('Y-m-d'))
                         ->whereNull('end_at')
                         ->whereHas('employee', function ($query) use ($employee) {
                             $query->where('id', $employee->id);
                         })
                         ->first();
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

        return $this->attendances()->where('shift_date', $date)->latest();
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
