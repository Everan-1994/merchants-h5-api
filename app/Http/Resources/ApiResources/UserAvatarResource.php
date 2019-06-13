<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;

class UserAvatarResource extends Resource
{
    public function toArray($request)
    {
        return [
            'avatar' => $this->user->avatar
        ];
    }
}