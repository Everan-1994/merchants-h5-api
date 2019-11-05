<?php

namespace App\Models;

class Suggest extends ApiBaseModel
{
    protected $fillable = [
        'user_id', 'user_name', 'user_tel', 'message'
    ];
}