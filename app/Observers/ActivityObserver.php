<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\ActivitySignUp;

class ActivityObserver
{
    public function deleted(Activity $activity)
    {
        ActivitySignUp::query()->where('activity_id', '=', $activity->id)->delete();
    }
}