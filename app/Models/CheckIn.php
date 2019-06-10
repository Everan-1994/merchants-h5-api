<?php

namespace App\Models;

class CheckIn extends ApiBaseModel
{
    const EXPIRED_LOTTERY = 0;
    const NO_LOTTERY = 1;
    const HAS_LOTTERY = 2;

    public static $lotteryStatus = [
        self::EXPIRED_LOTTERY => '已过期',
        self::NO_LOTTERY      => '未抽奖',
        self::HAS_LOTTERY     => '已抽奖',
    ];

    protected $fillable = [
        'user_id', 'status', 'check_in_time', 'check_in_times',
    ];
}