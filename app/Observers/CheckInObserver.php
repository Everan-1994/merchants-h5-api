<?php

namespace App\Observers;

use App\Models\CheckIn;
use App\Models\Winning;
use Carbon\Carbon;

class CheckInObserver
{
    public function created(Winning $winning)
    {
        $now = Carbon::now()->toDateString();

        $where = [
            'user_id'        => $winning->user_id,
            'check_in_times' => 4,
        ];

        CheckIn::query()
            ->where($where)
            ->whereBetween('check_in_time', [
                $now . ' 00:00:00',
                $now . ' 23:59:59',
            ])
            ->update(['status' => 2]);
    }
}