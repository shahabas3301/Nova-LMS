<?php

namespace Modules\Forumwise\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->whenHas('id'),
            'name'          => $this->whenHas('name'),
            'slug'          => $this->whenHas('slug'),
            'label_color'   => convertColor($this->whenHas('label_color')),
        ];
    }
}
