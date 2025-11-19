<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PopularTopicMediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'media' => $this->media->map(function ($media) {
                    return [
                        'path' => !empty($media->path) ? url(Storage::url($media->path)) : url(Storage::url('placeholder.png')),
                    ];
                }) ?? [],  
        ];
    }
}
