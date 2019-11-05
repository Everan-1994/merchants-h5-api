<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;
use App\Models\Zan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CommentResource extends Resource
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
            'id'         => $this->id,
            'comment'    => $this->comment,
            'like_times' => $this->like_times,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i'),
            'user'       => [
                'name'   => $this->user->name,
                'avatar' => $this->user->avatar,
            ],
            'is_owner'   => optional(Auth::guard('user')->user())->id == $this->user->id ?: false,
            'has_zan'    => self::hasZan($this->id),
        ];
    }

    /**
     * 判断是否点过赞
     * @param $id
     * @return bool
     */
    private function hasZan($id)
    {
        return Zan::query()->where([
            'user_id' => Auth::guard('user')->user()->id,
            'type'    => 3,
            'type_id' => $id,
        ])->exists();
    }
}