<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;

class ActivitySignResource extends Resource
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
            'id'             => $this->id,
            'contact_name'   => $this->contact_name,
            'contact_phone'  => $this->contact_phone,
            'sign_up_reason' => $this->sign_up_reason,
            'share_times'    => $this->share_times,
            'status'         => $this->status == 1 ? '已成功' : '未成功',
            'created_at'     => $this->created_at->toDateTimeString(),
        ];
    }
}