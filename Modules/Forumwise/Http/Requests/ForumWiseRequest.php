<?php

namespace Modules\Forumwise\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForumWiseRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() {
        return [
            'title'         => 'required|string|max:100',
            'status'        => 'required|boolean',
            'topic_role'    => 'required|array',
            'category_id'   => 'required',
            'description'   => 'required|string|max:150',
        ];
        
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void {
        $this->merge([
            'title'                     => sanitizeTextField($this->title),
            'status'                    => sanitizeTextField($this->status),
            'description'               => sanitizeTextField($this->description),
        ]);
    }
}
