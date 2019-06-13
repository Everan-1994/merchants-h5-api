<?php

namespace App\Models;

class TryUse extends ApiBaseModel
{
    const NOT_STARTED = 0;
    const IN_PROGRESS = 1;
    const END = 2;

    public static $applyStatus = [
        self::NOT_STARTED => '未开始',
        self::IN_PROGRESS => '进行中',
        self::END         => '已结束',
    ];

    protected $table = 'try_uses';

    protected $fillable = [
        'name', 'front_cover', 'stock', 'price', 'apply_start',
        'apply_end', 'product_intro', 'sort', 'status'
    ];

    /**
     * 关联试用参与者
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signs()
    {
        return $this->hasMany(UseSignUp::class, 'use_id', 'id')
            ->select(['id', 'use_id', 'user_id', 'contact_name'])
            ->with('user');
    }

    public function reports()
    {
        return $this->hasMany(ExperienceReport::class, 'type_id', 'id')
            ->where('type', '=', 2)
            ->with('user')
            ->select(['type_id', 'id', 'user_id', 'content', 'images', 'like_times', 'created_at']);
    }
}