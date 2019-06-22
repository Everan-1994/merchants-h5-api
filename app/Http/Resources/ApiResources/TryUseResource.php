<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class TryUseResource extends Resource
{
    const NOT_STARTED = 0;
    const IN_PROGRESS = 1;
    const END = 2;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'front_cover'   => $this->front_cover,
            'stock'         => $this->stock,
            'price'         => $this->price,
            'product_intro' => $this->when(!empty($request->route('id')), json_decode($this->product_intro, true)),
            'apply_end'     => Carbon::parse($this->apply_end)->toDateString(),
            'status'        => self::tryUseStatus($this->apply_start, $this->apply_end),
            'apply_status'          => $this->when(
                in_array('my', explode('/', $request->getRequestUri())),
                self::applyStatus($this->apply_status, $this->apply_start, $this->apply_end)
            ),
            'signs'         => UserAvatarResource::collection($this->whenLoaded('signs')),
            'reports'       => ExperienceReportResource::collection($this->whenLoaded('reports')),
        ];
    }

    /**
     * 活动进行状态
     * @param $start_date
     * @param $end_date
     * @return int
     */
    private function tryUseStatus($start_date, $end_date)
    {
        $now = Carbon::now(); // 当前日期
        $start_date = Carbon::parse($start_date); // 开始时间
        $end_date = Carbon::parse($end_date); // 结束时间

        $status = self::END; // 已结束

        if ($now->copy()->gte($start_date->copy()) && $now->copy()->lt($end_date->copy())) {
            $status = self::IN_PROGRESS; // 进行中
        }

        return $status;
    }

    /**
     * 个人申请状态
     * @param $status
     * @param $start_date
     * @param $end_date
     * @return string
     */
    private function applyStatus($status, $start_date, $end_date)
    {
        $now = Carbon::now(); // 当前日期
        $start_date = Carbon::parse($start_date); // 开始时间
        $end_date = Carbon::parse($end_date); // 结束时间

        $apply_status = '申请成功';

        if ($status < 1) {
            if ($now->copy()->gte($start_date->copy()) && $now->copy()->lt($end_date->copy())) {
                $apply_status = '申请中';
            } else {
                $apply_status = '申请失败';
            }
        }

        return $apply_status;
    }
}