<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ForumsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->whenHas('id'),
            'name'          => $this->whenHas('name'),  
            'label_color'   => convertColor($this->whenHas('label_color')),
            'forums'        => $this->whenLoaded('forums', function () {
                return $this->forums->map(function ($forum) {
                    return [
                        'id'            => $forum->id ?? null,
                        'title'         => $forum->title ?? null,
                        'description'   => $forum->description ?? null,
                        'status'        => $forum->status ?? null,
                        'category_id'   => $forum->category_id ?? null,
                        'created_by'    => $forum->created_by ?? null,
                        'slug'          => $forum->slug ?? null,
                        'topic_role'    => $forum->topic_role ?? null,
                        'topics_count'  => $forum->topics_count ?? null,
                        'posts_count'   => $forum->posts_count ?? null,
                        'media'         => $forum->media->map(function ($media) {
                            return [
                                'id'                => $media->id ?? null,
                                'mediaable_id'      => $media->mediaable_id ?? null,
                                'mediaable_type'    => $media->mediaable_type ?? null,
                                'type'              => $media->type ?? null,
                                'path'              => !empty($media->path) ? url(Storage::url($media->path)) : url(Storage::url('placeholder.png')),
                            ];
                        }) ?? [],
                    ];
                });
            }),
        ];
    }
}
