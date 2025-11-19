<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TopicContributorsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'image' => $this->whenLoaded('users', fn () => 
                !empty($this->users->profile->image) 
                    ? url(Storage::url($this->users->profile->image)) 
                    : url(Storage::url('placeholder.png'))
            ),
        ];
    }
}
