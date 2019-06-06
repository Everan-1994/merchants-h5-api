<?php

namespace App\Http\Resources;

class OperationLogResource extends Resource
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
            'id'        => $this->id,
            'username'  => $this->username,
            'agent'     => $this->agent,
            'uri'       => $this->uri,
            'route'     => $this->route,
            'params'    => $this->params,
            'method'    => $this->method,
            'ip'        => $this->ip,
            'ipInfo'    => $this->ipInfo,
            'data'      => $this->data,
            'code'      => $this->code,
            'message'   => $this->message,
            'createdAt' => $this->createdAt->toDateTimeString(),
        ];
    }
}