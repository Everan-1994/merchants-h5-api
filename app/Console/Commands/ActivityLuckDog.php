<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ActivitySignUp;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActivityLuckDog extends Command
{
    /**
     * 命令行执行命令.
     *
     * @var string
     */
    protected $signature = 'activity-luck-dog';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = '选出符合规则的活动参与者';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        // 获取报名到期的活动
        $activity = Activity::query()
            ->where('apply_end', $now->toDateTimeString())
            ->get();

        if ($activity->isNotEmpty()) {
            $activity_ids = $activity->pluck('id')->all(); // id 集合
            $activity_limits = $activity->pluck('limit', 'id')->all(); // id => 上限 键值对
            $activity_names = $activity->pluck('name', 'id')->all(); // id => 活动名称 键值对

            // 获取报名数据
            $activity_sign_ups = ActivitySignUp::query()->whereIn('activity_id', $activity_ids)
                ->orderBy('share_times', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();

            $activity_sign_ups_ids = $activity_sign_ups->pluck('id')->all(); // 报名 id 集合

            $notify = [];

            $activity_sign_ups->each(function ($item, $key) {
                
            });

        }
    }
}