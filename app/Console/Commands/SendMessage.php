<?php

namespace App\Console\Commands;

use App\Models\Notify;
use Illuminate\Console\Command;

class SendMessage extends Command
{
    /**
     * 命令行执行命令.
     *
     * @var string
     */
    protected $signature = 'seed-message';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = '发送活动和试用申请成功的消息提醒';

    protected $app;

    public function __construct()
    {
        parent::__construct();

        $this->app = app('wechat.official_account');
    }

    public function handle()
    {
        $notify = Notify::query()
            ->where('status', '=', 0)
            ->with('user')
            ->get();

        foreach ($notify as $key => $item) {
            $keyword1 = $item->type == 1 ?
                '你已成功报名参加活动『' . $item->title . '』。' :
                '你已成功获得『' . $item->title . '』的试用。';

            $this->app->template_message->send([
                'touser'      => $item->user->openid,
                'template_id' => env('TEMPLATE_LUCK_DOG'),
                'url'         => env('RED_URL') . '/myReport',
                'data'        => [
                    'first'    => '我们很高兴的通知你！',
                    'keyword1' => $keyword1,
                    'remark'   => '详情请到个人中心查看。',
                ],
            ]);
        }

        if (!empty($notify)) {
            try {
                // 改变消息状态
                Notify::query()->whereIn('id', $notify->pluck('id'))->update(['status' => 1]);
            } catch (\Exception $exception) {
                \Log::info('seed_message_error：' . $exception->getMessage());
            }
        }
    }
}