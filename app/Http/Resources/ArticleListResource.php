<?php

namespace App\Http\Resources;

class ArticleListResource extends Resource
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
            'id' => $this->id,
            'title' => $this->title,
            'flag' => $this->flag,
            'thumb' => $this->thumb,
            'content' => $this->content,
            'keyword' => $this->keyword,
            'description' => $this->description,
            'createdAt' => $this->createdAt->toDateTimeString(),
        ];
    }
}
