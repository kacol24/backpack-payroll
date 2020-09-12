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
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->employee->name,
            'start' => $this->start_at,
            'end'   => $this->end_at,
        ];
    }
}
