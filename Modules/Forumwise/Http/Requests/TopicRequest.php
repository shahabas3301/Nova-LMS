<?php

namespace Modules\Forumwise\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class TopicRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {   
        return [
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'tags'              => 'required|array',
            'tags.*'            => 'required|string|max:255',
            'status'            => 'required|boolean',
            'type'              => 'required|boolean',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'forum_id'          => request()->is('api/*') ? 'required|exists:fw__forums,id|integer' : 'nullable',
        ];
    }
}
