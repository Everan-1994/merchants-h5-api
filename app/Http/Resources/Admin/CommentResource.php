<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class CommentResource extends Resource
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
            'comment'    => $this->comment,
            'like_times' => $this->like_times,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'user'       => [
                'name'   => $this->user->name,
                'avatar' => $this->user->avatar,
            ]
        ];
    }
}