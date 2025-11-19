<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PopularTopicsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
 
        return [
            'id'            => $this->whenHas('id'),
            'title'         => $this->whenHas('title'),
            'slug'          => $this->whenHas('slug'),
            'description'   => $this->whenHas('description'),
            'tags'          => $this->whenHas('tags'),
            'type'          => $this->whenHas('type'),
            'status'        => $this->whenHas('status'),
            'forum_id'      => $this->whenHas('forum_id'),
            'created_by'    => $this->whenHas('created_by'),
            'posts_count'   => $this->whenHas('posts_count'),
            'media'         => $this->media->map(function ($media) {
                return [
                    'id'                => $media->id ?? null,
                    'mediaable_id'      => $media->mediaable_id ?? null,
                    'mediaable_type'    => $media->mediaable_type ?? null,
                    'type'              => $media->type ?? null,
                    'path'              => !empty($media->path) ? url(Storage::url($media->path)) : url(Storage::url('placeholder.png')),
                ];
            }) ?? [],  
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id'                => $this->creator->id ?? null,
                    'email'             => $this->creator->email ?? null,
                    'status'            => $this->creator->status ?? null,
                    'email_verified_at' => $this->creator->email_verified_at ?? null,
                    'profile' => $this->creator->profile ? [
                        'id'            => $this->creator->profile->id ?? null,
                        'first_name'    => $this->creator->profile->first_name ?? null,
                        'last_name'     => $this->creator->profile->last_name ?? null,
                        'image'         => !empty($this->creator->profile->image) ? url(Storage::url($this->creator->profile->image)) : url(Storage::url('placeholder.png')),
                        'user_id'       => $this->creator->profile->user_id ?? null,
                    ] : [],
                ];
            }),
        ];
    }
}
