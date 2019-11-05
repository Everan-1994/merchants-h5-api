<?php

namespace App\Models;

class Notify extends ApiBaseModel
{
    protected $fillable = [
        'sign_id', 'user_id', 'title', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}