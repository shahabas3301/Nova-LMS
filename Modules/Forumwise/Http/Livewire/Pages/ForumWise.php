<?php

namespace Modules\Forumwise\Http\Livewire\Pages;

use Modules\Forumwise\Http\Livewire\Forms\ForumWiseForm;
use Modules\Forumwise\Services\ForumWiseService;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ForumWise extends Component {

    public ForumWiseForm $form;
    use WithFileUploads;
    public $allowImgFileExt;
    public $allowImageSize;
    public $search   = '';
    public $categoriesList;
    private ?ForumWiseService $forumWiseService = null;
    public $categories;
    public $formId;
    public $user;
    public function boot() {

        $this->user = Auth::user();  
        $this->forumWiseService = new ForumWiseService($this->user);

    }

    public function getListeners()
    {
        return [
            'resetFormAndErrors' => 'resetForm',
        ];
    }

    public function render()
    {
        $this->categoriesList       = $this->forumWiseService->getForumList($this->search);
        return view('forumwise::livewire.pages.forum-wise');
    }
    public function mount() {
        $this->categories           = $this->forumWiseService->getCategoryList();
        $this->allowImgFileExt = config('forumwise.image.allowed_extensions');
        $this->allowImageSize = config('forumwise.image.max_size');
    }

    public function openForumModal($form = null)
    { 
        if(!empty($form)){
            $this->formId = $form['id'];
            $this->form->setInfo($form);
        }
        $this->dispatch('fw-toggleModel', id: 'fw-addforumpopup', action:'show');
        $this->dispatch('fw-initSelect2', target: '.fw-select2' );
    }

    public function closeForumModal()
    {
       
        $this->dispatch('fw-toggleModel', id: 'fw-addforumpopup', action:'hide');
        $this->dispatch('fw-modal-closed');
    }

    public function resetForm()
    {
        $this->formId = null;
        $this->resetErrorBag();
        $this->form->reset();
    }
    
    #[On('filterSearch')]
    public function searchForms($search)
    {
        $this->search = $search;
    }

    
    public function updatedForm($value, $key)
    {

        if( in_array($key, ['image']) ) {
            $mimeType = $value->getMimeType();  
            $type = explode('/', $mimeType);
            if($type[0] != 'image') {
                $this->dispatch('fw-showAlertMessage', type: `error`, message: __('validation.invalid_file_type', ['file_types' => fileValidationText($this->allowImgFileExt)]));
                $this->form->{$key} = null;
                return;
            }
        }
    }


    public function removePhoto()
    {
        $this->form->image = null;
    }
    

    public function removeTopicRole($role)
    {
        $this->form->topic_role = array_diff($this->form->topic_role, [$role]);
    }

    public function saveForum()
    {
        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            $this->closeForumModal();
            return;
        }
        $forumDetail = $this->form->updateInfo();
        $image = $forumDetail['image'];
        unset($forumDetail['image']);
        $foruminfo = $this->forumWiseService->storeOrUpdateForumDetail($forumDetail,$this->formId);
        $media = $this->forumWiseService->addForumMedia($foruminfo, $image);
        $this->dispatch('fw-showAlertMessage', type: 'success', title: __('general.success_title') , message: __('general.success_message'));
        $this->closeForumModal();
    }
}
