<?php

namespace App\Models;

class ActivitySignUp extends ApiBaseModel
{
    protected $table = 'activity_sign_ups';

    protected $fillable = [
        'user_id', 'activity_id', 'contact_name', 'contact_phone',
        'sign_up_reason', 'share_times', 'status'
    ];
}