<?php

namespace App\Console\Commands;

use App\Models\ReportNotify;
use Illuminate\Console\Command;

class SendReportMessage extends Command
{
    /**
     * 命令行执行命令.
     *
     * @var string
     */
    protected $signature = 'seed-report-message';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = '发送填写报告的消息提醒';

    protected $app;

    public function __construct()
    {
        parent::__construct();

        $this->app = app('wechat.official_account');
    }

    public function handle()
    {
        $notify = ReportNotify::query()
            ->where('status', '=', 0)
            ->with('user')
            ->get();

        foreach ($notify as $key => $item) {
            $keyword1 = $item->type == 1 ?
                '亲，记得填写『' . $item->title . '』的活动报告哟。' :
                '亲，记得填写『' . $item->title . '』的试用报告哟。';

            $this->app->template_message->send([
                'touser'      => $item->user->openid,
                'template_id' => env('TEMPLATE_WRITE_REPORT'),
                'url'         => '',
                'data'        => [
                    'first'    => '体验报告填写提醒！',
                    'keyword1' => $keyword1,
                    'remark'   => '如已填写体验报告，请忽略此信息。',
                ],
            ]);
        }

        if (!empty($notify)) {
            try {
                // 生成消息
                ReportNotify::query()->whereIn('id', $notify->pluck('id'))->update(['status' => 1]);
            } catch (\Exception $exception) {
                \Log::info('seed_report_message_error：' . $exception->getMessage());
            }
        }
    }
}