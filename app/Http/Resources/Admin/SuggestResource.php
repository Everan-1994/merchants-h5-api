<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;

class SuggestResource extends Resource
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
            'user_name'  => $this->user_name,
            'user_tel'   => $this->user_tel,
            'message'    => $this->message,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}