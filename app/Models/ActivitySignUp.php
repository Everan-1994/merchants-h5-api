<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class ActivitySignUp extends ApiBaseModel
{
    protected $table = 'activity_sign_ups';

    protected $fillable = [
        'user_id', 'activity_id', 'contact_name', 'contact_phone',
        'sign_up_reason', 'share_times', 'status'
    ];

    /**
     * 所属的活动
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }

    /**
     * 获取关联的活动报告
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne(ExperienceReport::class, 'sign_id', 'id')
            ->where([
                ['user_id', '=', Auth::guard('user')->user()->id],
                ['type', '=', 1]
            ]);
    }
}