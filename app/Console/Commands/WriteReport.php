<?php

namespace App\Console\Commands;

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
    protected $description = '提醒填写报告';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

    }
}