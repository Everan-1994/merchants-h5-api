<?php

namespace App\Console\Commands;

use App\Models\Notify;
use App\Models\TryUse;
use App\Models\UseSignUp;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TryUseLuckDog extends Command
{
    /**
     * 命令行执行命令.
     *
     * @var string
     */
    protected $signature = 'try-use-luck-dog';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = '选出符合规则的试用申请者';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        // 获取报名到期的活动
        $try_use = TryUse::query()
            ->where('apply_end', $now->copy()->format('Y-m-d H:i'))
            ->get();

        if (!empty($try_use)) {
            $try_use_ids = $try_use->pluck('id')->all(); // id 集合
            $try_use_stocks = $try_use->pluck('stock', 'id')->all(); // id => 上限 键值对
            $try_use_names = $try_use->pluck('name', 'id')->all(); // id => 活动名称 键值对

            // 获取报名数据
            $try_use_sign_ups = UseSignUp::query()->whereIn('use_id', $try_use_ids)
                ->where('status', '=', 0)
                ->orderBy('share_times', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();

            // 报名列表按活动id分组
            $try_use_sign_group = $try_use_sign_ups->groupBy('use_id');

            $sign_list = []; // 符合规则的报名列表
            $sign_ids = []; // 符合规则的报名 id 集合

            foreach ($try_use_sign_group as $key => $item) {
                $limit = $try_use_stocks[$key];
                $name = $try_use_names[$key];
                $signs = collect($item)->filter(function ($v, $i) use ($limit) {
                    return $i < $limit;
                });

                foreach ($signs as $k => $sign) {
                    $sign_ids[] = $sign->id;
                    $sign_list[] = [
                        'sign_id'    => $sign->id,
                        'user_id'    => $sign->user_id,
                        'title'      => $name,
                        'type'       => 1, // 试用
                        'status'     => 0, // 默认未发送
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
                    // 改变申请状态
                    UseSignUp::query()->whereIn('id', $sign_ids)->update(['status' => 1]);

                    \DB::commit();
                } catch (\Exception $exception) {
                    \DB::rollBack();
                    \Log::info('try_use_notify_error：' . $exception->getMessage());
                }
            }
        }
    }
}