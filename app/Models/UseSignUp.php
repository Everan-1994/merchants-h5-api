<?php

namespace App\Models;

class UseSignUp extends ApiBaseModel
{
    protected $table = 'use_sign_ups';

    protected $fillable = [
        'user_id', 'use_id', 'contact_name', 'contact_phone', 'province',
        'city', 'district', 'address', 'share_times', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->select(['id', 'name', 'avatar']);
    }
}