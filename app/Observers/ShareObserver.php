<?php

namespace App\Observers;

use App\Models\ActivitySignUp;
use App\Models\Share;
use App\Models\UseSignUp;
use Illuminate\Support\Facades\Auth;

class ShareObserver
{
    public function created(Share $share)
    {
        // 活动
        if ($share->type == 1) {
            $where = [
                'user_id' => Auth::guard('user')->user()->id,
                'activity_id' => $share->type_id
            ];
            ActivitySignUp::query()->where($where)->increment('share_times');
        }

        // 试用
        if ($share->type == 2) {
            $where = [
                'user_id' => Auth::guard('user')->user()->id,
                'use_id' => $share->type_id
            ];
            UseSignUp::query()->where($where)->increment('share_times');
        }
    }
}