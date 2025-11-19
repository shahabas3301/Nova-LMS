<div class="fw-banner-wraper"
    x-data="{ isDragging: false }"
    x-on:dragover.prevent="isDragging = true"
    x-on:drop="isDragging = false">
    <div class="fw-topic-main fw-forum-topic">

        @if($userStatus == 'invited')
            <div class="fw-forum_invited">
                <div class="fw-forum_invited_content">
                    <svg width="44" height="60" viewBox="0 0 44 60" fill="none">
                        <path d="M37.3999 15.7007C39.8299 15.7007 41.7998 17.6706 41.7998 20.1007V48.7C41.7998 53.5601 37.86 57.4999 33 57.4999H11.0001C6.14004 57.4999 2.2002 53.56 2.2002 48.7V11.3009C2.2002 6.44082 6.14003 2.50098 11.0001 2.50098H24.2C26.63 2.50098 28.6 4.4709 28.6 6.90091V11.3008C28.6 13.7308 30.5699 15.7007 32.9999 15.7007H37.3999Z" stroke="#EAEAEA" stroke-width="3.29995" stroke-miterlimit="10"/>
                        <path d="M21.5107 2.5H25.0235C27.3136 2.5 29.5135 3.39274 31.1559 4.98862L39.1271 12.7338C40.8324 14.3908 41.7946 16.6674 41.7946 19.0451V26.7655" stroke="#EAEAEA" stroke-width="3.29995" stroke-miterlimit="10"/>
                        <path d="M20.3477 44.298C26.7266 44.298 31.8976 39.127 31.8976 32.7483C31.8976 26.3695 26.7266 21.1985 20.3477 21.1985C13.9689 21.1985 8.79785 26.3695 8.79785 32.7483C8.79785 39.127 13.9689 44.298 20.3477 44.298Z" stroke="#EAEAEA" stroke-width="2.47496" stroke-miterlimit="10"/>
                        <path d="M18.5667 31.364C18.0823 32.1926 17.1825 32.7523 16.1584 32.7523C15.125 32.7523 14.2284 32.1926 13.7471 31.364" stroke="#EAEAEA" stroke-width="2.09005" stroke-miterlimit="10" stroke-linecap="round"/>
                        <path d="M26.95 31.3643C26.4668 32.1977 25.5643 32.7614 24.5366 32.7614C23.4994 32.7614 22.6002 32.1977 22.1201 31.3643" stroke="#EAEAEA" stroke-width="2.09005" stroke-miterlimit="10" stroke-linecap="round"/>
                        <path d="M20.3916 37.4304H20.4045" stroke="#EAEAEA" stroke-width="2.78674" stroke-linecap="round"/>
                        <path d="M35.1916 47.5973L31.8916 44.2974" stroke="#EAEAEA" stroke-width="2.47496" stroke-miterlimit="10" stroke-linecap="round"/>
                    </svg>
                    <h5>{{ __('forumwise::forum_wise.youve_been_invited') }}</h5>
                    <span>{{ __('forumwise::forum_wise.invitation_message') }}</span>
                    <div class="fw-forum_invited_btns">
                        <a href="javascript:void(0);" wire:click="invitationAction('accepted')" class="fw-bookmark fw-acceptbtn">{{ __('forumwise::forum_wise.accept_invitation') }}</a>
                        <a href="javascript:void(0);" wire:click="openDeclinePopup" class="fw-bookmark fw-declinebtn">{{ __('forumwise::forum_wise.decline') }}</a>
                    </div>
                </div>
            </div>
        @else
        <ul class="fw-david-list">
            @if($comments->count() > 0)
                @foreach ($comments as $comment)
                    @include('forumwise::components.comment', ['comment' => $comment])
                @endforeach
            @endif
        </ul>
        @endif
        @if($userStatus == 'accepted' || ($topic?->type == 'public' && auth()->check()) || $user?->role == 'admin' || $user?->id == $topic?->created_by)
            <div class="fw-topic-content-reply">
                <h3 class="fw-forum-tag-title">{{ __('forumwise::forum_wise.reply_to_topic') }}</h3>
                <div class="fw-forum-topic-description">
                    <div class="fw-forum-invalid @error('description') fw-invalid @enderror">
                        <div class="am-custom-editor fw-custom-editor" wire:ignore>
                            <textarea class="form-control" id="default-editor">
                          
                            </textarea>
                            
                        </div>
                        <x:forumwise::input-error field_name="description" />
                    </div>
                </div>
                <div class="fw-submit-main">
                    <a href="javascript:void(0)" class="fw-bookmark fw-submit" wire:click="savePost" wire:loading.class="fw-btn_disable">{{ __('forumwise::forum_wise.submit_reply') }}</a>
                </div>
            </div>           
          @endif
          @if($topic?->type == 'public' && !auth()->check())
          <div class="fw-forum_replylogin">
                <div class="fw-forum_replylogin_content">
                    <figure>
                    <img src="{{ asset('modules\forumwise\images\replyloginoser.png') }}" alt="{{ __('Image') }}" />  
                    </figure>
                    <h5>{{ __('forumwise::forum_wise.sign_in_to_reply') }}</h5>
                    <span>{{ __('forumwise::forum_wise.login_message') }}</span>
                    <a href="{{ route('login') }}" class="fw-bookmark fw-loginbtn">{{ __('forumwise::forum_wise.login') }}</a>
                </div>
            </div>
          @endif
          
          @if(($topic?->type == 'private' && $user?->role != config('forumwise.db.roles.administrator')) && ($topic?->type == 'private' && (!auth()->check()) || ($user?->id != $topic->created_by && $userStatus == null) || $userStatus == 'rejected'))
                <div class="fw-privatesection">
                    <div class="fw-privatesection_content">
                        <figure>
                            <img src="{{ asset('modules\forumwise\images\lockIcon.png') }}" alt="image">
                        </figure>
                        <h5>{{ __('forumwise::forum_wise.private_forum') }}</h5>
                        <span>{{ __('forumwise::forum_wise.private_content_restricted') }}</span>
                    </div>
                </div>
          @endif
    </div>
    <div class="fw-forum-top-users-main">
        @include('forumwise::components.popularTopics', ['popularTopics' => $relatedTopics ,'title' => 'Related topics Topics' ,'description' => 'Related Discussions in the Community'])
        <div class="fw-forum-users-tags">
            <h3 class="fw-forum-tag-title">Tags</h3>
            <div class="fw-forum-tags-content">
                @foreach($topic?->tags as $tag)
                    <a href="javascript:void(0)">{{ $tag }}</a>
                @endforeach
            </div>
        </div>
        <!-- <div class="fw-forum-users-tags fw-forum-comunity">
            <h3 class="fw-forum-tag-title">Still need help? </h3>
            <em>Don't be shy and ask the community.</em>
            <a href="javascript:void(0)" wire:click="openAskQuestionPopup" fw-data-target="#fw-addqestion-popup" class="fw-bookmark fw-here">Ask your question here!</a>
        </div> -->
    </div>
    @include('forumwise::components.declinePopup')
    @include('forumwise::components.askQuestionPopup')
    @include('forumwise::components.inviteUserPopup')
</div>
@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/forumwise/summernote/summernote-lite.min.css') }}">
@endpush
@push('scripts')
    <script defer src="{{ asset('modules/forumwise/summernote/summernote-lite.min.js') }}"></script>
@endpush
@push('scripts')
<script>
function toggleReplySection(commentId) {
    const allReplySections = document.querySelectorAll('.fw-topic-content');
    allReplySections.forEach(section => {
        if (section.id !== `reply-section-${commentId}`) {
            section.style.display = 'none';
        }
    });
    const replySection = document.getElementById(`reply-section-${commentId}`);
    if (replySection) {
        replySection.style.display = replySection.style.display === 'none' ? 'block' : 'none';
    } else {
        const replySection = `
            <div id="reply-section-${commentId}" class="fw-topic-content fw-close-${commentId}" wire:key="post-reply-section-${commentId}">
                <h3 class="fw-forum-tag-title">Reply</h3>
                <div class="fw-forum-topic-description">
                    <div class="am-custom-editor fw-custom-editor" wire:ignore>
                        <textarea class="form-control" id="post-reply-editor-${commentId}">
                         
                        </textarea>
                    </div>
                </div>
                <div class="fw-submit-main">
                    <a href="javascript:void(0)" class="fw-bookmark fw-submit" wire:key="post-reply-button-${commentId}" wire:click="savePostReply(${commentId})" wire:loading.class="fw-btn_disable">Submit Reply</a>
                </div>
                
                <a href="javascript:void(0);" class="fw-removemodal" onclick="closeModal(${commentId})"><i class="fw-icon-multiply-02"></i></a>
            </div>
        `;

        const postElement = document.getElementById(`reply-count-${commentId}`).closest('.fw-villy-about');
        const replyElement = postElement.querySelector('.fw-villy-stats');
        replyElement.insertAdjacentHTML('afterend', replySection);
 
        $(`#post-reply-editor-${commentId}`).summernote({
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
            ],
            height: 200,
            width: '100%',
            callbacks: {
                onChange: function(contents, $editable) {
                    @this.set('replyContent', contents,false);
                }
            }
        });
    }
}

document.addEventListener('livewire:initialized', function() {
    var component = @this;
    $('#default-editor').summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
        ],
        height: 400,
        width: '100%',
        callbacks: {
            onChange: function(contents, $editable) {
                component.set('description', contents);
            }
        }
    });
});

function closeModal(id) {
    const modal = document.querySelector(`.fw-close-${id}`); 
    if (modal) {
        modal.style.display = 'none'; 
    }
}

</script>
@endpush
