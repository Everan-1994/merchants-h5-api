<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class ActivityResource extends Resource
{
    const NOT_STARTED = 0;
    const IN_PROGRESS = 1;
    const END = 2;

    protected static $status = [
        self::NOT_STARTED => '未开始',
        self::IN_PROGRESS => '进行中',
        self::END         => '已结束',
    ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'limit'          => $this->limit,
            'activity_start' => $this->activity_start,
            'activity_end'   => $this->activity_end,
            'sort'           => $this->sort,
            'status'         => self::activityStatus($this->activity_start, $this->activity_end),
        ];
    }

    /**
     * 活动进行状态
     * @param $start_date
     * @param $end_date
     * @return int
     */
    private function activityStatus($start_date, $end_date)
    {
        $now = Carbon::now(); // 当前日期
        $start_date = Carbon::parse($start_date); // 开始时间
        $end_date = Carbon::parse($end_date); // 结束时间

        $status = self::$status[self::END]; // 已结束

        if ($now->copy()->gte($start_date->copy()) && $now->copy()->lt($end_date->copy())) {
            $status = self::$status[self::IN_PROGRESS]; // 进行中
        }

        if ($now->copy()->lt($start_date->copy())) {
            $status = self::$status[self::NOT_STARTED]; // 未开始
        }

        return $status;
    }
}