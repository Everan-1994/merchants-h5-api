<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class MyTryUseReportResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->try_use->id,
            'name' => $this->try_use->name,
            'front_cover' => $this->try_use->front_cover,
            'stock' => $this->try_use->stock,
            'price' => $this->try_use->price,
            'apply_end' => Carbon::parse($this->apply_end)->format('Y-m-d'), // 申请结束时间
            'is_write' => optional($this->report)->id > 0 ? 1 : 0
        ];
    }
}