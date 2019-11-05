<?php

namespace App\Http\Resources\ApiResources;

use App\Http\Resources\Resource;

class TopicResource extends Resource
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
            'title'       => $this->title,
            'front_cover' => $this->front_cover,
            $this->mergeWhen(!empty($request->route('id')), [
                'content'  => $this->content,
                'comments' => CommentResource::collection($this->whenLoaded('comments')),
            ]),
            'users'       => self::uniqueUser(UserAvatarResource::collection($this->whenLoaded('comments'))),
        ];
    }

    private function uniqueUser($users)
    {
        if (!empty($users)) {
            $unique = collect($users)->unique('id');

            return $unique->values()->all();
        }
    }
}