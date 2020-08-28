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
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

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

    public function allowances()
    {
        return $this->belongsToMany(Allowance::class);
    }

    public function deductions()
    {
        return $this->belongsToMany(Deduction::class);
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
}
