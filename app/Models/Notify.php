<?php

namespace App\Models;

class Notify extends ApiBaseModel
{
    protected $fillable = [
        'sign_id', 'user_id', 'title',
    ];
}