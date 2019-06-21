<?php

namespace App\Models;


class Winning extends ApiBaseModel
{
    protected $fillable = [
        'user_id', 'prize_name', 'contact_name', 'contact_phone',
        'province', 'city', 'district', 'address', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}