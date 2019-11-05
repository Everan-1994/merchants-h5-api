<?php

namespace App\Observers;

use App\Models\Block;
use App\Models\BlockItem;
use App\Models\Watch;

class WatchObserver
{
    public function created(Watch $watch)
    {
        // 视频观看计数
        BlockItem::query()->where('id', $watch->video_id)->increment('watch_times');

        // 视频模块观看计数
        Block::query()
            ->where('id', BlockItem::query()->where('id', $watch->video_id)->value('blockId'))
            ->increment('watch_times');
    }
}