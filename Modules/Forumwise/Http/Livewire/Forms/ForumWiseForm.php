<?php

namespace Modules\Forumwise\Http\Livewire\Forms;
use Modules\Forumwise\Http\Requests\ForumWiseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Form;


class ForumWiseForm extends Form
{


    public string $title             = '';
    public bool $status              = true;
    public string $description       = '';
    public array $topic_role        = [];
    public string $category_id       = '';
    public $image;





    private ?ForumWiseRequest $forumRequest = null;

    public function boot()
    {
        $this->user                 = Auth::user();
        $this->forumRequest         = new ForumWiseRequest();
    }

    public function rules(): array
    {

        return $this->forumRequest->rules();
    }

    public function setInfo($forumDetail)
    {
        $this->title        = $forumDetail['title'] ?? '';
        $this->status       = $forumDetail['status'] == 'active' ? true : false;
        $this->category_id  = $forumDetail['category_id'] ?? '';
        $this->description  = $forumDetail['description'] ?? '';
        $this->topic_role   = json_decode($forumDetail['topic_role'] ?? '[]', true);
        $this->image        = $forumDetail['media'][0]['path'] ?? '';
    }

    public function updateInfo()
    {
        if (!empty($this->image) && $this->image instanceof \Illuminate\Http\UploadedFile) {
            $randomNumber = Str::random(40);
            $imageName               = $randomNumber . '_' . $this->image->getClientOriginalName();
            $image                  = $this->image->storeAs('forum', $imageName, getStorageDisk());
        } else {
            $image = $this->image;
        }
        $forumDetail = [
            'title'                     => $this->title,
            'status'                    => $this->status ? 'active' : 'inactive',
            'category_id'               => $this->category_id,
            'topic_role'                => json_encode($this->topic_role),
            'description'               => $this->description,
            'image'                     => $image,
            'created_by'                => Auth::user()->id,
        ];
        $rules = $this->rules();
        $this->validate($rules);
        return $forumDetail;
    }


}
