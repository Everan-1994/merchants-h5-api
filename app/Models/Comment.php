<?php

namespace App\Models;

class Comment extends ApiBaseModel
{
    protected $fillable = [
        'user_id', 'topic_id', 'comment', 'like_times'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}