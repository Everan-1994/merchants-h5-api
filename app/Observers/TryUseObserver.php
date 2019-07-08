<?php

namespace App\Observers;


use App\Models\TryUse;
use App\Models\UseSignUp;

class TryUseObserver
{
    public function deleted(TryUse $tryUse)
    {
        UseSignUp::query()->where('use_id', '=', $tryUse->id)->delete();
    }
}