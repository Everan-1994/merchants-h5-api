<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

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

    /**
     * 获取关联的活动
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function try_use()
    {
        return $this->belongsTo(TryUse::class, 'use_id', 'id');
    }

    /**
     * 获取关联的活动报告
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne(ExperienceReport::class, 'type_id', 'id')
            ->where([
                ['user_id', '=', Auth::guard('user')->user()->id],
                ['type', '=', 2]
            ]);
    }
}