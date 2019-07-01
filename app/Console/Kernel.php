<?php

namespace App\Console;

use App\Console\Commands\ActivityLuckDog;
use App\Console\Commands\SendMessage;
use App\Console\Commands\SendReportMessage;
use App\Console\Commands\TryUseLuckDog;
use App\Console\Commands\WriteReport;
use App\Console\Commands\WriteUseReport;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ActivityLuckDog::class,
        TryUseLuckDog::class,
        WriteReport::class,
        WriteUseReport::class,
        SendMessage::class,
        SendReportMessage::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('activity-luck-dog')
            ->timezone('Asia/Shanghai')
            ->everyMinute() // 每分钟
            ->runInBackground(); // 并行执行

        $schedule->command('try-use-luck-dog')
            ->timezone('Asia/Shanghai')
            ->everyMinute() // 每分钟
            ->runInBackground(); // 并行执行

        $schedule->command('write-report')
            ->timezone('Asia/Shanghai')
            ->everyMinute() // 每分钟
            ->runInBackground(); // 并行执行

        $schedule->command('write-use-report')
            ->timezone('Asia/Shanghai')
            ->everyMinute() // 每分钟
            ->runInBackground(); // 并行执行

        $schedule->command('seed-message')
            ->timezone('Asia/Shanghai')
            ->everyMinute() // 每分钟
            ->runInBackground(); // 并行执行

        $schedule->command('seed-report-message')
            ->timezone('Asia/Shanghai')
            ->everyMinute() // 每分钟
            ->runInBackground(); // 并行执行
    }
}
