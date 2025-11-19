<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TopicDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->whenHas('id'),
            'title'          => $this->whenHas('title'),
            'slug'           => $this->whenHas('slug'),
            'description'    => $this->whenHas('description'),
            'tags'           => $this->whenHas('tags'),
            'updated_at'     => $this->whenHas('updated_at'),
            'created_by'     => $this->whenHas('created_by'),
            'posts_count'    => $this->whenHas('posts_count'),
            'comments_count' => $this->whenHas('comments_count'),
            'views_count'    => $this->whenHas('views_count'),
            'votes_count'    => $this->whenHas('votes_count'),
            'media'          => $this->whenLoaded('media', function () {
                $media = $this->media->first(); 
                return $media ? url(Storage::url($media->path)) : url(Storage::url('placeholder.png')); 
            }),
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'profile' => $this->creator->profile ? [
                        'first_name' => $this->creator->profile->first_name ?? null,
                        'last_name'  => $this->creator->profile->last_name ?? null,
                        'image'      => !empty($this->creator->profile->image) ? url(Storage::url($this->creator->profile->image)) : url(Storage::url('placeholder.png')),
                    ] : [],
                ];
            }),

        ];
    }
}
