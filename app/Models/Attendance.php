<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Venturecraft\Revisionable\RevisionableTrait;

class Attendance extends Model
{
    const TYPE_CLOCK_IN = 'in';

    const TYPE_CLOCK_OUT = 'out';

    use CrudTrait;
    use RevisionableTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'attendances';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = [
        'start_at',
        'end_at',
        'shift_date',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($obj) {
            \Storage::disk('public')->delete($obj->selfie);
        });
    }

    public function identifiableName()
    {
        return $this->employee->name . " [{$this->start_at->format('Y-m-d')}]";
    }

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
    public function getHoursWorkedAttribute()
    {
        return round($this->real_hours_worked);
    }

    public function getRealHoursWorkedAttribute()
    {
        if (! $this->end_at) {
            return 0;
        }

        $hoursWorked = $this->start_at->diffInSeconds($this->end_at ?? now()) / 60 / 60;

        return $hoursWorked;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setStartAtAttribute($value)
    {
        if ($value) {
            $this->attributes['start_at'] = Date::parse($value);
        }
    }

    public function setEndAtAttribute($value)
    {
        if ($value) {
            $this->attributes['end_at'] = Date::parse($value);
        }
    }

    public function setSelfieInAttribute($value)
    {
        $attribute_name = "selfie_in";
        $disk = "public";
        $destination_path = "selfie";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setSelfieOutAttribute($value)
    {
        $attribute_name = "selfie_out";
        $disk = "public";
        $destination_path = "selfie";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function uploadFileToDisk($value, $attribute_name, $disk, $destination_path)
    {
        // if a new file is uploaded, delete the file from the disk
        if (request()->hasFile($attribute_name) &&
            $this->{$attribute_name} &&
            $this->{$attribute_name} != null) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        // if the file input is empty, delete the file from the disk
        if (is_null($value) && $this->{$attribute_name} != null) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if (request()->hasFile($attribute_name) && request()->file($attribute_name)->isValid()) {
            // 1. Generate a new file name
            $file = request()->file($attribute_name);
            $new_file_name = now()->format('Ymd_H-i-s').'_'.strtoupper(Str::slug($attribute_name)).'_'.strtoupper(Str::slug($this->employee->name)).'.jpg';

            // 2. Move the new file to the correct path
            $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

            // 3. Save the complete path to the database
            $this->attributes[$attribute_name] = $file_path;
        }
    }
}
