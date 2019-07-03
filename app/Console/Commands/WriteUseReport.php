<?php

namespace App\Console\Commands;

use App\Models\ReportNotify;
use App\Models\TryUse;
use App\Models\UseSignUp;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WriteUseReport extends Command
{
    /**
     * 命令行执行命令.
     *
     * @var string
     */
    protected $signature = 'write-use-report';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = '提醒填写试用报告';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        // 获取试用申请结束2天后的列表
        $try_use = TryUse::query()
            // ->where('apply_end', $now->copy()->addDays(2)->toDateTimeString())
            ->where('apply_end', $now->copy()->addMinutes(5)->toDateTimeString())
            ->get();

        if (!empty($try_use)) {
            $try_use_ids = $try_use->pluck('id')->all(); // id 集合
            $try_use_names = $try_use->pluck('name', 'id')->all(); // id => 活动名称 键值对

            // 获取报名数据
            $try_use_sign_ups = UseSignUp::query()->whereIn('use_id', $try_use_ids)
                ->where('status', '=', 1)
                ->get();

            // 报名列表按活动id分组
            $try_use_sign_group = $try_use_sign_ups->groupBy('use_id');

            $sign_list = []; // 符合规则的报名列表

            foreach ($try_use_sign_group as $key => $item) {
                $name = $try_use_names[$key];

                foreach ($item as $k => $sign) {
                    $sign_list[] = [
                        'sign_id'    => $sign->id,
                        'user_id'    => $sign->user_id,
                        'title'      => $name,
                        'type'       => 2, // 试用
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