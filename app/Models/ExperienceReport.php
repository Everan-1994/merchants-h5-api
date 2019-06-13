<?php

namespace App\Models;

use Carbon\Carbon;

class ExperienceReport extends ApiBaseModel
{
    protected $table = 'experience_reports';

    protected $fillable = [
        'user_id', 'type', 'type_id', 'content', 'images', 'like_times'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->select(['id', 'name', 'avatar']);
    }

    /**
     * 活动
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity_report()
    {
        return $this->belongsTo(Activity::class , 'type_id', 'id')
            ->select(['id', 'name']);
    }

    /**
     * 试用
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function try_use_report()
    {
        return $this->belongsTo(TryUse::class , 'type_id', 'id')
            ->select(['id', 'name']);
    }

    public function getCreatedAtAttribute($date)
    {
        // 默认1天前输出完整时间，否则输出人性化的时间
        if (Carbon::now() > Carbon::parse($date)->addDays(1)) {
            return Carbon::parse($date)->format('Y-m-d H:i');
        }

        return Carbon::parse($date)->diffForHumans();
    }
}