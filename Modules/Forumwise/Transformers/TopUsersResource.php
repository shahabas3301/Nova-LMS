<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
class TopUsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'image'         => !empty($this->image) ? url(Storage::url($this->image)) : url(Storage::url('placeholder.png')),
            'topic_count'   => $this->topic_count,
            'post_count'    => $this->post_count,
            'total_count'   => $this->total_count,
        ];
    }
}
