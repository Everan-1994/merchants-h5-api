<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ActivitySignUp;
use App\Models\Notify;
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
            ->where('apply_end', $now->copy()->format('Y-m-d H:i'))
            ->get();

        if (!empty($activity)) {
            $activity_ids = $activity->pluck('id')->all(); // id 集合
            $activity_limits = $activity->pluck('limit', 'id')->all(); // id => 上限 键值对
            $activity_names = $activity->pluck('name', 'id')->all(); // id => 活动名称 键值对

            // 获取报名数据
            $activity_sign_ups = ActivitySignUp::query()->whereIn('activity_id', $activity_ids)
                ->where('status', '=', 0)
                ->orderBy('share_times', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();

            // 报名列表按活动id分组
            $activity_sign_group = $activity_sign_ups->groupBy('activity_id');

            $sign_list = []; // 符合规则的报名列表
            $sign_ids = []; // 符合规则的报名 id 集合

            foreach ($activity_sign_group as $key => $item) {
                $limit = $activity_limits[$key];
                $name = $activity_names[$key];
                $signs = collect($item)->filter(function ($v, $i) use ($limit) {
                    return $i < $limit;
                });

                foreach ($signs as $k => $sign) {
                    $sign_ids[] = $sign->id;
                    $sign_list[] = [
                        'sign_id'    => $sign->id,
                        'user_id'    => $sign->user_id,
                        'title'      => $name,
                        'type'       => 1,
                        'status'     => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            if (!empty($sign_list)) {
                \DB::beginTransaction();
                try {
                    // 生成消息
                    Notify::query()->insert($sign_list);
                    // 改变报名状态
                    ActivitySignUp::query()->whereIn('id', $sign_ids)->update(['status' => 1]);

                    \DB::commit();
                } catch (\Exception $exception) {
                    \DB::rollBack();
                    \Log::info('activity_notify_error：' . $exception->getMessage());
                }
            }
        }
    }
}