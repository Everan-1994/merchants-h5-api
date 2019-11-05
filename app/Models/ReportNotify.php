<?php

namespace App\Models;

class ReportNotify extends ApiBaseModel
{
    protected $fillable = [
        'sign_id', 'user_id', 'title', 'status', 'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}