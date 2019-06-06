<?php

namespace App\Http\Resources;

class ArticleResource extends Resource
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
            'catId' => $this->catId,
            'category' => (new CategoryResource($this->whenLoaded('category')))['name'],
            'user' => (new AdminUserResource($this->whenLoaded('user')))['realname'],
            'author' => $this->author,
            'isPublic' => $this->isPublic,
            'sort' => $this->sort,
            'thumb' => $this->thumb,
            'content' => $this->content,
            'description' => $this->description,
            'flag' => $this->flag,
            'keyword' => $this->keyword,
            'views' => $this->views,
            'createdAt' => $this->createdAt->toDateTimeString(),
            'time' => $this->createdAt->timestamp,
        ];
    }
}
