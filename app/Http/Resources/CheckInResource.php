<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'status' => CheckIn::$lotteryStatus[$this->status],
            'check_in_times' => $this->check_in_times,
            'check_in_time' => Carbon::parse($this->check_in_time)->toDateString(),
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}