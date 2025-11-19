<?php

namespace Modules\CourseBundles\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CourseBundlesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->whenHas('id'),
            'title'                     => $this->whenHas('title'),
            'price'                     => $this->whenHas('price'),
            'discount_percentage'       => $this->when($this->discount_percentage > 0, $this->discount_percentage),
            'final_price'               => $this->when($this->discount_percentage > 0, $this->final_price),
            'short_description'         => $this->whenHas('short_description'),
            'courses_count'             => $this->whenHas('courses_count'),
            'courses_content_length'    => $this->whenHas('courses_sum_content_length', fn () => getCourseDuration($this->courses_sum_content_length)),
            'thumbnail'                 => $this->whenLoaded('thumbnail', fn () => [
                'url'                   => $this->thumbnail?->path ? url(Storage::url($this->thumbnail?->path)) : null,
            ]),
            'instructor' => $this->whenLoaded('instructor', function () {
                return [
                    'profile' => $this->instructor?->relationLoaded('profile') ? [
                        'id'            => $this->instructor?->profile?->id,
                        'short_name'    => $this->instructor?->profile?->short_name,
                        'image'         => $this->instructor?->profile?->image ? url(Storage::url($this->instructor?->profile?->image)) : null,
                    ] : null,
                ];
            }),
        ];
    }
}
