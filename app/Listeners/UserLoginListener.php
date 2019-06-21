<?php

namespace App\Listeners;

use App\Events\UserLoginEvent;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoginListener
{
    /**
     * @param UserLoginEvent $loginEvent
     */
    public function handle(UserLoginEvent $loginEvent)
    {
        $user_id = $loginEvent->getUserId();

        $now = Carbon::now();

        $user_log = UserLog::query()->where('user_id', '=', $user_id)
        ->whereBetween('created_at', [
            $now->copy()->toDateString() . ' 00:00:00',
            $now->copy()->toDateString() . ' 23:59:59',
        ]);

        if (!$user_log->exists()) {
            $user_log->create([
                'user_id' => $user_id
            ]);
        }
    }
}
