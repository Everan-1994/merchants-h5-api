<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;

class ExperienceReportResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'images' => $this->when(!empty($this->images), json_decode($this->images)),
            'content' => $this->content,
            'like_times' => $this->like_times,
            'created_at' => $this->created_at,
            'user' => $this->user,
            'activity_report' => $this->whenLoaded('activity_report'),
            'try_use_report' => $this->whenLoaded('try_use_report')
        ];
    }
}