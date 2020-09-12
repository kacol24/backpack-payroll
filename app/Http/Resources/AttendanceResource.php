<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AttendanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'employee_id'        => $this->employee_id,
            'shift_date'         => $this->shift_date->format('Y-m-d'),
            'start_at'           => $this->start_at->format('Y-m-d H:i:s'),
            'formatted_start_at' => $this->start_at->format('d F Y H:i:s'),
            'end_at'             => optional($this->end_at)->format('Y-m-d H:i:s'),
            'formatted_end_at'   => optional($this->end_at)->format('d F Y H:i:s'),
            'comment'            => $this->comment,
            'selfie_in'          => $this->selfie_in,
            'selfie_in_url'      => asset(Storage::url($this->selfie_in)),
            'selfie_out'         => $this->selfie_out,
            'selfie_out_url'     => asset(Storage::url($this->selfie_out)),
        ];
    }
}
