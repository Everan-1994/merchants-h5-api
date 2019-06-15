<?php

namespace App\Models;

class Share extends ApiBaseModel
{
    protected $fillable = [
        'user_id', 'type', 'type_id'
    ];
}