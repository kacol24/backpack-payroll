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
        'notes',
    ];

    // protected $hidden = [];
    protected $dates = [
        'paid_at',
        'period',
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
        return format_money($this->gross_pay);
    }

    public function getFormattedTotalAllowancesAttribute()
    {
        return format_money($this->total_allowances);
    }

    public function getFormattedTotalDeductionsAttribute()
    {
        return format_money($this->total_deductions);
    }

    public function getFormattedNetPayAttribute()
    {
        return format_money($this->net_pay);
    }

    public function getTotalEarningsAttribute()
    {
        return $this->gross_pay + $this->total_allowances;
    }

    public function getFormattedTotalEarningsAttribute()
    {
        return format_money($this->total_earnings);
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
