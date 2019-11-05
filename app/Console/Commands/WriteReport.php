<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ActivitySignUp;
use App\Models\ReportNotify;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WriteReport extends Command
{
    /**
     * 命令行执行命令.
     *
     * @var string
     */
    protected $signature = 'write-report';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = '提醒填写活动报告';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        // 获取活动到期的活动列表
        $activity = Activity::query()
            ->where('activity_end', $now->copy()->format('Y-m-d H:i'))
            ->get();

        if (!empty($activity)) {
            $activity_ids = $activity->pluck('id')->all(); // id 集合
            $activity_names = $activity->pluck('name', 'id')->all(); // id => 活动名称 键值对

            // 获取报名数据
            $activity_sign_ups = ActivitySignUp::query()->whereIn('activity_id', $activity_ids)
                ->where('status', '=', 1)
                ->get();

            // 报名列表按活动id分组
            $activity_sign_group = $activity_sign_ups->groupBy('activity_id');

            $sign_list = []; // 符合规则的报名列表

            foreach ($activity_sign_group as $key => $item) {
                $name = $activity_names[$key];

                foreach ($item as $k => $sign) {
                    $sign_list[] = [
                        'sign_id'    => $sign->id,
                        'user_id'    => $sign->user_id,
                        'title'      => $name,
                        'type'       => 1, // 活动
                        'status'     => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            if (!empty($sign_list)) {
                try {
                    // 生成消息
                    ReportNotify::query()->insert($sign_list);
                } catch (\Exception $exception) {
                    \Log::info('activity_report_notify_error：' . $exception->getMessage());
                }
            }
        }
    }
}