<?php

namespace App\Models;

class Topic extends ApiBaseModel
{
    const ACTIVE = 1; // 正常
    const FREEZE = 0; // 冻结

    protected $fillable = [
        'title', 'front_cover', 'content', 'sort', 'status'
    ];

    /**
     * 获取话题的所有评论和评论的用户
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->with('user');
    }
}