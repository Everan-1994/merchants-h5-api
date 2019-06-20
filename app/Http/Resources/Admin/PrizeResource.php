<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;

class PrizeResource extends Resource
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
            'id'          => $this->id,
            'prize_name'  => $this->prize_name,
            'prize_num'   => $this->prize_num,
            'prize_image' => $this->prize_image,
            'probability' => $this->probability,
            'status'      => $this->status > 0 ? '是' : '否',
        ];
    }
}