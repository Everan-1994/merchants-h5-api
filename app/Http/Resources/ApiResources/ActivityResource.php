<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class ActivityResource extends Resource
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
            'id'                    => $this->id,
            'name'                  => $this->name,
            'front_cover'           => $this->front_cover,
            'activity_apply_status' => self::activityApplyStatus($this->apply_start, $this->apply_end),
            'apply_time_prompt'     => self::getApplyTimePrompt($this->apply_start, $this->apply_end),
            $this->mergeWhen(!empty($request->route('id')), [
                // 存在 参数 id 时 合并 显示
                'address'        => $this->address,
                'limit'          => $this->limit,
                'activity_intro' => $this->activity_intro,
                'content'        => $this->content
            ]),
            'signs'                 => $this->whenLoaded('signs'),
            'reports'               => ExperienceReportResource::collection($this->whenLoaded('reports')),
        ];
    }

    /**
     * 报名时间段
     * @param $start_date
     * @param $end_date
     * @return string
     */
    private function getApplyTimePrompt($start_date, $end_date)
    {
        $now = Carbon::now(); // 当前日期
        $start_date = Carbon::parse($start_date); // 开始时间
        $end_date = Carbon::parse($end_date); // 结束时间

        $diff_date = '';
        $d = 0; // 时
        $h = 0; // 分
        $i = 0; // 秒

        if ($now->copy()->gte($start_date->copy()) && $now->copy()->lt($end_date->copy())) {
            $diff_date = $end_date->copy()->diff($now->copy());
        }

        if (!empty($diff_date)) {
            $d = $diff_date->d;
            $h = $diff_date->h;
            $i = $diff_date->i;
        }

        return sprintf('%u天%u时%u分后结束', $d, $h, $i);
    }

    private function activityApplyStatus($start_date, $end_date)
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
}