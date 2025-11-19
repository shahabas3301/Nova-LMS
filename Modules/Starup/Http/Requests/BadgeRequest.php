<?php

namespace Modules\Starup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
{

    public function rules(): array
    {
    

        return [
            'badgeName'             => 'required|string|max:255',
            'badgeDescription'      => 'required|string',
            'selectedCategory'      => 'required',
            'badgeImage'            => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'badgeRating.regex' => __('starup::starup.rating_validation_message'),
            'selectedCategory.required' => __('starup::starup.category_required'),
            'badgeImage.required' => __('starup::starup.badge_icon_required'),
            'badgeName.required' => __('starup::starup.name_required'),


        ];
    }

     /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'badgeName'                => sanitizeTextField($this->badgeName),
            'badgeDescription'         => sanitizeTextField($this->badgeDescription),
            'selectedCategory'         => sanitizeTextField($this->selectedCategory),
            'badgeImage'               => sanitizeTextField($this->badgeImage),
        ]);
    }
}
