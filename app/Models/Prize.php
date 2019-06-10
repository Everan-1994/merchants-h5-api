<?php

namespace App\Models;

class Prize extends ApiBaseModel
{
    const ACTIVE = 1; // 激活
    const FREEZE = 0; // 冻结

    protected $fillable = [
        'prize_name', 'prize_num', 'prize_image', 'probability', 'status'
    ];
}