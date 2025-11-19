<?php

namespace Modules\Forumwise\Http\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Forumwise\Models\Comment;
use Modules\Forumwise\Services\ForumPostServices;
use Modules\Forumwise\Services\CommentsServices;
use Modules\Forumwise\Services\ForumTopicService;
use App\Jobs\SendNotificationJob;
class ForumPost extends Component
{
    use WithFileUploads;
    public $slug;
    public $image = null;
    public $allowImgFileExt;
    public $description;
    public $allowImageSize;
    public $questionTitle;
    public $questionCategory;
    public $questionDescription;
    public $user;
    public $replyContent;
    public $invitedUserList;
    public $comments;
    public $message;
    public $showReplyForm = null;
    public $topic;
    public $questionImage = null;
    public $email = null;
    public $userList = [];
    protected $forumPostServices = null;
    protected $commentsServices = null;
    protected $forumTopicService = null;
    public $userStatus;
    public $relatedTopics;
    public function boot()
    {

        $this->forumPostServices = new ForumPostServices();
        $this->commentsServices = new CommentsServices();
        $this->forumTopicService = new ForumTopicService($this->user);
    }

    public function mount($slug, $topic)
    {
        $this->user = Auth::user();
        $this->slug = $slug;
        $this->topic = $topic;
        $this->allowImgFileExt = config('forumwise.image.allowed_extensions');
        $this->allowImageSize = config('forumwise.image.max_size');
        $this->dispatch('fw-initSelect2', target: '.fw-select2' );
        if($this->user) {
            $this->forumPostServices->createView($this->topic->id, $this->user->id);
        }
        $this->relatedTopics = $this->forumTopicService->getRelatedTopic($this->topic);
    }
    
    public function render()
    {
        $this->comments = $this->forumPostServices->getPost($this->topic->id);
        if($this->user) {
            $this->userStatus = $this->forumPostServices->getTopicUserStatus($this->topic->id, $this->user->id);
        }
        return view('forumwise::livewire.pages.forum-post');
    }

    public function validatePost()
    {
        $this->validate([
            'description' => 'required',
        ]);
    }

    public function openAskQuestionPopup()
    {
        if(!Auth::check()) {
            $this->dispatch('fw-showAlertMessage', type: 'info', message: __('Please login to ask a question.'));
            return;
        }
        $this->dispatch('fw-toggleModel', id: 'fw-askQuestionpopup', action:'show');
    }

    public function closeAskQuestionModal()
    {  
        $this->resetErrorBag();
        $this->dispatch('fw-toggleModel', id: 'fw-askQuestionpopup', action:'hide');
    }
    public function removePhoto()
    {
        $this->questionImage = null;
        $this->image = null;
    }

    public function resetPost()
    {
        $this->image = null;
        $this->description = '';
        $this->email = '';
        $this->message = '';
        $this->recipients= [];
    }

    #[On('openInviteUserPopup')] 
    public function openInviteUserPopup()
    {        
        $this->dispatch('fw-initSelect2', target: '.fw-select2' );
        $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'show');
    }

    #[On('openContributorPopup')] 
    public function openContributorPopup()
    {      
        $this->dispatch('fw-toggleModel', id: 'fw-contributor-popup', action:'show');
    }

    public function closeContributorPopup()
    {
        $this->dispatch('fw-toggleModel', id: 'fw-contributor-popup', action:'hide');
    }
    
    #[On('add-vote')] 
    public function addVote()
    {       
        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            return;
        }
        
        if(!Auth::check()) {
            $this->dispatch('fw-showAlertMessage', type: 'info', message: __('Please login to vote this topic.'));
            return;
        }
        

        $existingVote = $this->topic->votes()->where('user_id', Auth::user()->id)->first();
        if ($existingVote) {
            $this->dispatch('fw-showAlertMessage', type: 'info', message: __('You have already voted for this topic.'));
            return;
        }
        $this->topic->votes()->create([
            'user_id'   => Auth::user()->id,
            'type'      => 'vote',
        ]);
        $this->forumPostServices->addTopicUser($this->topic->id, Auth::user()->id);
        $votes_count = $this->topic->votes()->count();
        $this->dispatch('show-votes', $votes_count);
        $this->topic->updated_at = now();
        $this->topic->save();
        $this->dispatch('fw-showAlertMessage', type: 'success', message: __('You have voted for this topic.'));
    }


    public function updatedImage($value, $key)
    {
        $mimeType = $value->getMimeType();  
        $type = explode('/', $mimeType);
        if($type[0] != 'image') {
            $this->dispatch('fw-showAlertMessage', type: `error`, message: __('validation.invalid_file_type', ['file_types' => fileValidationText($this->allowImgFileExt)]));
            $this->image = null;
            return;
        }
    }

    public function updatedQuestionImage($value, $key)
    {
        
        $mimeType = $value->getMimeType();  
        $type = explode('/', $mimeType);
        if($type[0] != 'image') {
            $this->dispatch('fw-showAlertMessage', type: `error`, message: __('validation.invalid_file_type', ['file_types' => fileValidationText($this->allowImgFileExt)]));
            $this->questionImage = null;
            return;
        }
    }

    public function likeComment($commentId)
    {
        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            return;
        }

        if(!Auth::check()) {
            $this->dispatch('fw-showAlertMessage', type: 'info', message: __('Please login to like this comment.'));
            return;
        }
        $comment = Comment::find($commentId);
        $existingLike = $comment->likes()->where('user_id', Auth::user()->id)->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            $comment->likes()->create([ 
                'user_id' => Auth::user()->id,
            ]);
        }
        $this->forumPostServices->addTopicUser($this->topic->id, Auth::user()->id);
        $this->topic->updated_at = now();
        $this->topic->save();
    }

    public function toggleReplyForm($postId)
    {
        $this->showReplyForm = $this->showReplyForm === $postId ? null : $postId;
    }

    public function closeInviteUserPopup()
    {   
        $this->resetErrorBag();
        $this->resetPost();
        $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'hide');
    }

    public function savePostReply($commentId)
    {
        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            return;
        }

        if(!Auth::check()) {
            $this->dispatch('fw-showAlertMessage', type: 'info', message: __('Please login to reply to this comment.'));
            return;
        }
        $this->validate([
            'replyContent' => 'required',
        ]);

        $comment = $this->commentsServices->createComment([
            'topic_id' => $this->topic->id,
            'parent_id' => $commentId,
            'description' => $this->replyContent,
            'created_by' => Auth::user()->id,
        ]);
        $this->topic->updated_at = now();
        $this->topic->save();
        $this->forumPostServices->addTopicUser($this->topic->id, Auth::user()->id);
    }

    public function savePost()
    {
   
        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            return;
        }

        $this->validatePost();
        if (!empty($this->image) && $this->image instanceof \Illuminate\Http\UploadedFile) {
            $randomNumber = Str::random(40);
            $imageName               = $randomNumber . '_' . $this->image->getClientOriginalName();
            $image                  = $this->image->storeAs('post', $imageName, getStorageDisk());
        } else {
            $image = $this->image;
        }
        $postDetail = [
            'topic_id'                  => $this->topic->id,
            'description'               => $this->description,
            'created_by'                => Auth::user()->id,
        ];

        $post = $this->forumPostServices->createPost($postDetail);
        $this->forumPostServices->addPostMedia($post, $image);
        $this->forumPostServices->addTopicUser($this->topic->id, Auth::user()->id);
        $this->topic->updated_at = now();
        $this->topic->save();
        $this->resetPost(); 
   
    }

    public function validateInvitation()
    {
        $this->validate([
            'email' => 'required|email',
            'message'    => 'required',
        ]);
    }

    public function sendInvite()
    {

        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'hide');
            return;
        }

        $this->validateInvitation();
        $invitedUser = $this->forumPostServices->getInvitedUser($this->email);
        if(!$invitedUser) {
            $this->dispatch('fw-showAlertMessage', type: 'error', message: __('User not found.'));
            $this->resetPost();
            $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'hide');
            return;
        }

        if($invitedUser->role == config('forumwise.db.roles.administrator')) {
            $this->dispatch('fw-showAlertMessage', type: 'error', message: __('You cannot invite an administrator.'));
            $this->resetPost();
            $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'hide');
            return;
        }
        
        if($invitedUser->id == $this->topic->created_by) {
            if($this->user->role == config('forumwise.db.roles.administrator'))
            {
                $this->dispatch('fw-showAlertMessage', type: 'error', message: __('You cannot invite the topic author.'));
            } else {
                $this->dispatch('fw-showAlertMessage', type: 'error', message: __('You cannot invite yourself.'));
            }
            $this->resetPost();
            $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'hide');
            return;
        }

        $user = $this->forumPostServices->invitedUser($this->topic->id,$invitedUser->id);
        if($user) {
            $this->dispatch('fw-showAlertMessage', type: 'error', message: __('You have already invited this user.'));
            $this->resetPost();
            $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'hide');
            return;
        }
        $this->forumPostServices->sendInvite($invitedUser->id,$this->topic->id);        
        $this->dispatch('fw-toggleModel', id: 'fw-invite-popup', action:'hide');
        $this->dispatch('fw-showAlertMessage', type: 'success', message: __('Invitation sent successfully.'));
        $data = [
            'userName' => $invitedUser->profile->first_name . ' ' . $invitedUser->profile->last_name,
            'forumTopicTitle' => $this->topic->title,
            'inviteLink' => route('topic', $this->topic->slug), 
            'message'    => $this->message,
            ];
        dispatch(new SendNotificationJob('inviteUser', $invitedUser, $data));
        $this->resetPost();
    }
    public function openDeclinePopup()
    {
        $this->dispatch('fw-toggleModel', id: 'fw-delete-popup', action:'show');
    }
    public function closeDeclinePopup()
    {
        $this->dispatch('fw-toggleModel', id: 'fw-delete-popup', action:'hide');
    }
    public function invitationAction($status)
    {
        $response = isDemoSite();
        if( $response ){
            $this->dispatch('fw-showAlertMessage', type: 'error', title:  __('general.demosite_res_title') , message: __('general.demosite_res_txt'));
            return;
        }

        $this->forumPostServices->acceptInvitation($this->topic->id, Auth::user()->id,$status);
        if($status == 'accepted') {
            $this->dispatch('fw-showAlertMessage', type: 'success', message: __('Invitation accepted successfully.'));
        } else {
            $this->dispatch('fw-showAlertMessage', type: 'success', message: __('Invitation rejected successfully.'));
            return redirect()->route('forums');
        }
    }

}
