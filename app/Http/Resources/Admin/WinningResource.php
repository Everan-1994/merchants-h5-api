<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Resource;

class WinningResource extends Resource
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
            'id'            => $this->id,
            'user_name'     => $this->user->name,
            'prize_name'    => $this->prize_name,
            'contact_name'  => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'province'      => $this->province,
            'city'          => $this->city,
            'district'      => $this->district,
            'address'       => $this->address,
            'created_at'    => $this->created_at->toDateTimeString(),
            'status'        => self::statusArr($this->status),
        ];
    }

    private function statusArr($status)
    {
        switch ($status) {
            case 1:
                $res = [
                    'status' => $status,
                    'res'    => '处理中',
                ];
                break;
            case 2:
                $res = [
                    'status' => $status,
                    'res'    => '已完成',
                ];
                break;
            default:
                $res = [
                    'status' => $status,
                    'res'    => '未处理',
                ];
                break;
        }

        return $res;
    }
}