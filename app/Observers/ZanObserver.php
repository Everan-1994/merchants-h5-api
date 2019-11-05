<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\ExperienceReport;
use App\Models\Zan;

class ZanObserver
{
    public function created(Zan $zan)
    {
        // 活动报告 & 试用报告
        if ($zan->type < 3) {
            ExperienceReport::query()->where('id', $zan->type_id)->increment('like_times');
        }

        // 评论
        if ($zan->type == 3) {
            Comment::query()->where('id', $zan->type_id)->increment('like_times');
        }
    }

    public function deleted(Zan $zan)
    {
        // 活动报告 & 试用报告
        if ($zan->type < 3) {
            ExperienceReport::query()->where('id', $zan->type_id)->decrement('like_times');
        }

        // 评论
        if ($zan->type == 3) {
            Comment::query()->where('id', $zan->type_id)->decrement('like_times');
        }
    }
}