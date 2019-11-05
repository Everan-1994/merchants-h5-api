<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;
use Carbon\Carbon;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'sex'         => $this->sex,
            'status'      => $this->status,
            'avatar'      => $this->avatar,
            'activities'  => count($this->activities),
            'try_uses'    => count($this->try_uses),
            'reports'     => count($this->reports),
            'login_times' => self::logs($this->user_logs),
            'created_at'  => $this->createdAt->toDateTimeString(),
        ];
    }

    private function logs($logs)
    {
        if (!empty($logs)) {
            $now = Carbon::now();

            $_logs = collect($logs)->whereBetween('created_at', [
                $now->copy()->subDays(6)->toDateString() . ' 00:00:00',
                $now->copy()->toDateString() . ' 23:59:59',
            ])->count();

            return $_logs;
        }

        return 0;

    }
}