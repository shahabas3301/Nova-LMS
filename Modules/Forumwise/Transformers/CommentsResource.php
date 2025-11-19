<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CommentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->whenHas('id'),
            'description'   => $this->whenHas('description'),
            'topic_id'      => $this->whenHas('topic_id'),
            'parent_id'     => $this->whenHas('parent_id'),
            'created_by'    => $this->whenHas('created_by'),
            'created_at'    => $this->whenHas('created_at'),
            'updated_at'    => $this->whenHas('updated_at'),
            'likes_count'   => $this->whenHas('likes_count'),
            'replies_count' => $this->whenHas('replies_count'),
            'replies'       => $this->whenLoaded('replies', function () {
                return $this->replies->map(function ($reply) {
                    return [
                        'id'            => $reply->id,
                        'description'   => $reply->description,
                        'topic_id'      => $reply->topic_id,
                        'parent_id'     => $reply->parent_id,
                        'created_by'    => $reply->created_by,
                        'created_at'    => $reply->created_at,
                        'updated_at'    => $reply->updated_at,
                        'likes_count'   => $reply->likes_count,
                        'replies_count' => $reply->replies_count,
                        'replies'       => $reply->replies->map(function ($reply) {
                            return [
                                'id'            => $reply->id,
                                'description'   => $reply->description,
                                'topic_id'      => $reply->topic_id,
                                'parent_id'     => $reply->parent_id,
                                'created_by'    => $reply->created_by,
                                'created_at'    => $reply->created_at,
                                'updated_at'    => $reply->updated_at,
                                'likes_count'   => $reply->likes_count,
                                'replies_count' => $reply->replies_count,
                            ];
                        }),
                        'creator' => $this->whenLoaded('creator', function () {
                            return [
                                    'profile'       => $this->creator->profile ? [
                                    'first_name'    => $this->creator->profile->first_name ?? null,
                                    'last_name'     => $this->creator->profile->last_name ?? null,
                                    'image'         => !empty($this->creator->profile->image) ? url(Storage::url($this->creator->profile->image)) : url(Storage::url('placeholder.png')),
                                ] : [],
                            ];
                        }),
                    ];
                });
            }),
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'profile'           => $this->creator->profile ? [
                        'first_name'    => $this->creator->profile->first_name ?? null,
                        'last_name'     => $this->creator->profile->last_name ?? null,
                        'image'         => !empty($this->creator->profile->image) ? url(Storage::url($this->creator->profile->image)) : url(Storage::url('placeholder.png')),
                    ] : [],
                ];
            }),
        ];
    }
}
