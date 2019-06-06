<?php

namespace App\Http\Resources;

class CategoryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'pid' => $this->pid,
            'id' => $this->id,
            'name' => $this->name,
            'floor' => $this->floor,
            'flag' => $this->flag,
            'sort' => $this->sort,
            'thumb' => $this->when($this->thumb, $this->thumb),
            'content' => $this->when($this->content, $this->content),
            'createdAt' => $this->createdAt->toDateTimeString(),
            'time' => $this->createdAt->timestamp,
            'views' => $this->views,
            'children' => CategoryResource::collection($this->whenLoaded('allChildren')),
        ];
    }
}
