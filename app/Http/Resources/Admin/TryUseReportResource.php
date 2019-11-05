<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class TryUseReportResource extends Resource
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
            'id'         => $this->id,
            'name'       => $this->user->name,
            'content'    => $this->content,
            'images'     => $this->images,
            'like_times' => $this->like_times,
            'created_at' => $this->created_at,
        ];
    }
}