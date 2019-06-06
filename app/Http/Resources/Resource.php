<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}