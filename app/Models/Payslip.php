<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'payslips';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    //protected $guarded = ['id'];

    protected $fillable = [
        'employee_id',
        'name',
        'period',
        'gross_pay',
        'total_allowances',
        'total_deductions',
        'net_pay',
        'paid_at',
        'allowances',
        'deductions',
    ];

    // protected $hidden = [];
    protected $dates = [
        'paid_at',
    ];

    protected $casts = [
        'allowances' => 'object',
        'deductions' => 'object',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getFormattedGrossPayAttribute()
    {
        return 'Rp' . number_format($this->gross_pay, 0, ',', '.');
    }

    public function getFormattedTotalAllowancesAttribute()
    {
        return 'Rp' . number_format($this->total_allowances, 0, ',', '.');
    }

    public function getFormattedTotalDeductionsAttribute()
    {
        return 'Rp' . number_format($this->total_deductions, 0, ',', '.');
    }

    public function getFormattedNetPayAttribute()
    {
        return 'Rp' . number_format($this->net_pay, 0, ',', '.');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setGrossPayAttribute($value)
    {
        $this->attributes['gross_pay'] = strip_money_mask($value);
    }

    public function setTotalAllowancesAttribute($value)
    {
        $this->attributes['total_allowances'] = strip_money_mask($value);
    }

    public function setTotalDeductionsAttribute($value)
    {
        $this->attributes['total_deductions'] = strip_money_mask($value);
    }

    public function setNetPayAttribute($value)
    {
        $this->attributes['net_pay'] = strip_money_mask($value);
    }
}
