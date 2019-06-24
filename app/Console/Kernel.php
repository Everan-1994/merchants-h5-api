<?php

namespace App\Console;

use App\Console\Commands\ActivityLuckDog;
use App\Console\Commands\TryUseLuckDog;
use App\Console\Commands\WriteReport;
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
        WriteReport::class
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
            ->everyMinute(); // 每分钟
    }
}
