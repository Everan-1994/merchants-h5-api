<?php

namespace App\Http\Resources;

class AdminUserResource extends Resource
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
            'name' => $this->username,
            'isEnable' => $this->isEnable,
            'realname' => $this->realname,
        ];
    }
}