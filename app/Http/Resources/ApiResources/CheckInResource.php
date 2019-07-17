<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;
use App\Models\CheckIn;
use Carbon\Carbon;

class CheckInResource extends Resource
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
            'id'             => $this->id,
            'status'         => self::checkStatus($this->status, $this->check_in_time, $this->check_in_times),
            'check_in_times' => $this->check_in_times,
            'check_in_time'  => Carbon::parse($this->check_in_time)->toDateString(),
            'created_at'     => Carbon::parse($this->created_at)->toDateTimeString(),
        ];
    }

    /**
     * 抽奖状态
     * @param $status
     * @param $check_in_time
     * @param $check_in_times
     * @return int
     */
    private function checkStatus($status, $check_in_time, $check_in_times)
    {
        switch ($status) {
            case 0:
                if ($check_in_times == 4 && Carbon::today()->gt(Carbon::parse(Carbon::parse($check_in_time)->toDateString()))) {
                    return -1; // 已过期
                }

                if ($check_in_times == 4 && Carbon::today()->eq(Carbon::parse(Carbon::parse($check_in_time)->toDateString()))) {
                    return 1; // 待抽奖
                }

                return 0; // 正常签到
                break;
            default:
                return 2; // 已抽奖
                break;
        }
    }
}