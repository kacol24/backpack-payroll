<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'on_shift' => (bool) $this->isOnShift(),
        ];
    }
}
