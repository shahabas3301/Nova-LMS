<?php

namespace Modules\Forumwise\Http\Livewire\Forms;
use Modules\Forumwise\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Form;


class TopicForm extends Form
{
    
    public string $title             = '';
    public bool $status              = true;
    public bool $type                = true;
    public string $forum_id          = '';
    public string $description       = '';
    public array $tags               = [];
    public string $category_id       = '';
    public $image;





    private ?TopicRequest $topicRequest = null;

    public function boot()
    {
        $this->user                 = Auth::user();
        $this->topicRequest         = new TopicRequest();
    }

    public function rules(): array
    {

        return $this->topicRequest->rules();
    }

    public function updateInfo()
    {
        
        if (!empty($this->image) && $this->image instanceof \Illuminate\Http\UploadedFile) {
            $randomNumber = Str::random(40);
            $imageName               = $randomNumber . '_' . $this->image->getClientOriginalName();
            $image                   = $this->image->storeAs('topic', $imageName, getStorageDisk());
        } else {
            $image = $this->image;
        }
        $topicDetail = [
            'title'                     => $this->title,
            'status'                    => $this->status ? 'active' : 'inactive',
            'type'                      => $this->type ? 'public' : 'private',
            'tags'                      => $this->tags,
            'description'               => $this->description,
            'image'                     => $image,
            'created_by'                => Auth::user()->id,
        ];
        $rules = $this->rules();
        $this->validate($rules);
        return $topicDetail;
    }


}
