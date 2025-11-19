<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RelatedTopicsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'media' => $this->whenLoaded('media', function () {
                $media = $this->media->first(); 
                return $media ? url(Storage::url($media->path)) : url(Storage::url('placeholder.png')); 
            }),
        ];
    }
}
