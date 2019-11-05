<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;

class VideoResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'front_cover' => $this->front_cover,
            'watch_times' => $this->watch_times
        ];
    }
}