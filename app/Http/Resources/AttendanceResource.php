<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'employee_id' => $this->employee_id,
            'shift_date'  => $this->shift_date->format('Y-m-d'),
            'start_at'    => $this->start_at->format('Y-m-d H:i:s'),
            'end_at'      => optional($this->end_date)->format('Y-m-d H:i:s'),
            'comment'     => $this->comment,
        ];
    }
}
