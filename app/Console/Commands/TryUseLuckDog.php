<?php

namespace App\Console\Commands;

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

    }
}