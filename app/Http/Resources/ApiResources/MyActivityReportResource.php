<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class MyActivityReportResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->activity->id,
            'name'        => $this->activity->name,
            'front_cover' => $this->activity->front_cover,
            'created_at'  => Carbon::parse($this->created_at)->format('Y年m月d日'), // 申请时间
            'sign_id'     => $this->id, // 报名id
            'is_write'    => optional($this->report)->id > 0 ? 1 : 0,
            'report_id'   => $this->report->id
        ];
    }
}