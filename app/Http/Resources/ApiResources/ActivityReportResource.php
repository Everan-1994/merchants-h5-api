<?php

namespace App\Http\Resources\ApiResources;

use App\Models\ApiBaseModel;

class ActivityReportResource extends ApiBaseModel
{
    public function toArray()
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'front_cover'           => $this->front_cover,
            'join_time'           => $this->front_cover,
        ];
    }
}