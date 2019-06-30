<?php

namespace App\Models;

class Watch extends ApiBaseModel
{
    protected $table = 'watchs';

    protected $fillable = [
        'user_id', 'video_id', 'ip'
    ];
}