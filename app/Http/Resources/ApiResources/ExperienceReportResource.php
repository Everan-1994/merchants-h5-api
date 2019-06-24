<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;
use App\Models\ExperienceReport;
use App\Models\Zan;
use Illuminate\Support\Facades\Auth;

class ExperienceReportResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'images'          => $this->when(!empty($this->images), $this->images),
            'content'         => $this->content,
            'like_times'      => $this->like_times,
            'created_at'      => $this->created_at,
            'user'            => $this->user,
            'activity_report' => $this->whenLoaded('activity_report'),
            'try_use_report'  => $this->whenLoaded('try_use_report'),
            'has_zan'         => self::hasZan($this->id)
        ];
    }

    /**
     * 判断是否点过赞
     * @param $id
     * @return bool
     */
    private function hasZan($id)
    {
        $er = ExperienceReport::query()->find($id);

        return Zan::query()->where([
            'user_id' => Auth::guard('user')->user()->id,
            'type'    => $er['type'],
            'type_id' => $er['type_id'],
        ])->exists();
    }
}