<?php

namespace Modules\Forumwise\Http\Livewire\Pages;
use Livewire\WithFileUploads;
use Modules\Forumwise\Http\Livewire\Forms\TopicForm;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Modules\Forumwise\Services\ForumWiseService;
use Livewire\Attributes\On;
use Modules\Forumwise\Services\ForumTopicService;

class ForumTopic extends Component {

    use WithFileUploads;

    public TopicForm $form;
    public $allowImgFileExt;    
    public $allowImageSize;
    public $user;
    public $search = '';
    public $forum;
    public $topUsers;
    public $roles;
    protected $forumWiseService = null;
    protected $forumTopicService = null;
    public $filterType = 'all';
    public $slug;
    public $popularTopics;

    public function boot()
    { 
        $this->user = Auth::user();
        $this->forumWiseService = new ForumWiseService($this->user);
        $this->forumTopicService = new ForumTopicService($this->user);
    }
    
    public function mount($slug, $roles)
    {
        $this->slug = $slug;
        $this->roles = $roles;
        $this->allowImgFileExt = config('forumwise.image.allowed_extensions');
        $this->allowImageSize = config('forumwise.image.max_size');
        $this->forum = $this->forumWiseService->getForumBySlug($this->slug);
    }
    
    public function filterTopics($type)
    {
        $this->filterType = $type;
    }
    
    public function removePhoto()
    {
        $this->form->image = null;
    }
    
    
    public function render()
    {
        $this->popularTopics = $this->forumTopicService->getPopularTopics();
        $this->topUsers = $this->forumTopicService->getTopUsers();
        $topics = $this->forumTopicService->getTopicList($this->search, $this->slug, $this->filterType);
        return view('forumwise::livewire.pages.forum-topic', compact('topics'));
    }   
    
    #[On('openAddTopicPopup')] 
    public function openAddTopicPopup()
    {
        $this->dispatch('fw-initSelect2', target: '.fw-select2' );
        $this->dispatch('fw-toggleModel', id: 'fw-addtopic-popup', action:'show');
    }

    public function closeTopicModal()
    {
        $this->form->reset();   
        $this->resetErrorBag();
        $this->dispatch('fw-toggleModel', id: 'fw-addtopic-popup', action:'hide');
    }  
    

    public function saveTopic()
    {
        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            $this->closeTopicModal();
            return;
        }
        $topicDetail = $this->form->updateInfo();
        $image = $topicDetail['image'];
        unset($topicDetail['image']);
        $topicDetail['forum_id'] = $this->forum->id;
        $topicinfo = $this->forumTopicService->storeOrUpdateTopicDetail($topicDetail);
        $media = $this->forumTopicService->addTopicMedia($topicinfo, $image);
        $this->forumTopicService->addTopicUser($topicinfo->id, $this->user->id);
        $this->dispatch('fw-showAlertMessage', type: 'success', title: __('general.success_title') , message: __('general.success_message'));
        $this->closeTopicModal();
    }
}