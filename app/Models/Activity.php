<?php

namespace App\Models;

class Activity extends ApiBaseModel
{
    const NOT_STARTED = 0;
    const IN_PROGRESS = 1;
    const END = 2;

    public static $activityApplyStatus = [
        self::NOT_STARTED => '未开始',
        self::IN_PROGRESS => '进行中',
        self::END         => '已结束',
    ];

    protected $table = 'activities';

    protected $fillable = [
        'name', 'front_cover', 'limit', 'address', 'apply_start', 'status', 'sort',
        'apply_end', 'activity_start', 'activity_end', 'activity_intro', 'content',
    ];

    /**
     * 关联的活动体验报告
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(ExperienceReport::class, 'type_id', 'id')
            ->where('type', '=', 1)
            ->with('user')
            ->select(['type_id', 'id', 'user_id', 'content', 'images', 'like_times', 'created_at']);
    }

    /**
     * 关联活动参与者
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signs()
    {
        return $this->hasMany(ActivitySignUp::class, 'activity_id', 'id')->select(['id', 'activity_id', 'contact_name', 'status']);
    }

    /**
     * 个人关联活动参与
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sign()
    {
        return $this->hasOne(ActivitySignUp::class, 'activity_id', 'id')->select(['id', 'activity_id', 'contact_name', 'status']);
    }
}