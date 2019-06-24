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
                'address'            => $this->address,
                'limit'              => $this->limit,
                'activity_intro'     => json_decode($this->activity_intro, true),
                'content'            => $this->content,
                'activity_time'      => self::activityTime($this->activity_start, $this->activity_end),
                'activity_apply_end' => Carbon::parse($this->apply_end)->toDateTimeString(),
            ]),
            'signs'                 => $this->whenLoaded('signs'),
            'reports'               => ExperienceReportResource::collection($this->whenLoaded('reports')),
            'apply_status'          => $this->when(
                in_array('my', explode('/', $request->getRequestUri())),
                self::applyStatus($this->apply_status, $this->apply_start, $this->apply_end)
            ),
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

    /**
     * 活动报名状态
     * @param $start_date
     * @param $end_date
     * @return int
     */
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

    /**
     * 申请状态
     * @param $status
     * @param $start_date
     * @param $end_date
     * @return int
     */
    private function applyStatus($status, $start_date, $end_date)
    {
        $now = Carbon::now(); // 当前日期
        $start_date = Carbon::parse($start_date); // 开始时间
        $end_date = Carbon::parse($end_date); // 结束时间

        $apply_status = 2; // 申请成功

        if ($status < 1) {
            if ($now->copy()->gte($start_date->copy()) && $now->copy()->lt($end_date->copy())) {
                $apply_status = 1; // 申请中
            } else {
                $apply_status = 0; // 申请失败
            }
        }

        return $apply_status;
    }

    private function activityTime($activity_start, $activity_end)
    {
        $start = Carbon::parse($activity_start); // 活动开始时间
        $end = Carbon::parse($activity_end); // 活动结束时间

        // 判断是否同一日
        if ($start->copy()->toDateString() == $end->copy()->toDateString()) {
            return $start->format('Y年m月d日 H:i') . '-' . $end->format('H:i');
        }

        return $start->format('Y年m月d日 H:i') . '-' . $end->format('Y年m月d日 H:i');
    }
}