<?php

namespace App\Models;

class Zan extends ApiBaseModel
{
    protected $fillable = [
        'user_id', 'type', 'type_id'
    ];
}