<?php

namespace App\Models;

class Activity extends ApiBaseModel
{
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
        return $this->hasMany(ExperienceReport::class, 'type_id', 'id');
    }

    /**
     * 关联活动参与者
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signs()
    {
        return $this->hasMany(ActivitySignUp::class, 'activity_id', 'id')->select(['id', 'activity_id', 'contact_name']);
    }
}