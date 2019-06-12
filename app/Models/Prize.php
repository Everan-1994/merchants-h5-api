<?php

namespace App\Models;

class Prize extends ApiBaseModel
{
    const ACTIVE = 1; // 奖品
    const FREEZE = 0; // 非奖品

    protected $fillable = [
        'prize_name', 'prize_num', 'prize_image', 'probability', 'status'
    ];
}