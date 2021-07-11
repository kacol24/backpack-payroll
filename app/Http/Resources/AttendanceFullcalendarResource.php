<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceFullcalendarResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        JsonResource::withoutWrapping();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $descriptionTemplate = '<div class="text-left">
                                    In: {start_at}<br>
                                    Out: {end_at}<br>
                                    Hours: {hours_worked}
                                </div>';

        $descriptionTemplate = str_replace([
            '{start_at}',
            '{end_at}',
            '{hours_worked}',
        ], [
            optional($this->start_at)->format('H:i:s'),
            optional($this->end_at)->format('H:i:s'),
            $this->hours_worked,
        ], $descriptionTemplate);

        return [
            'title'       => $this->employee->name,
            'start'       => $this->start_at,
            'end'         => $this->end_at,
            'description' => $descriptionTemplate,
        ];
    }
}
