@php
    if(auth()?->user()?->role == config('forumwise.db.roles.administrator')){
         $extends = config('forumwise.admin_layout.layout')  ? config('forumwise.admin_layout.layout') : 'forumwise::layouts.form-wise' ;
         $content = config('forumwise.admin_layout.content_section')  ? config('forumwise.admin_layout.content_section') : 'content' ;
         $styles = config('forumwise.admin_layout.style_section')  ? config('forumwise.admin_layout.style_section') : 'styles' ;
         $scripts = config('forumwise.admin_layout.scripts_section')  ? config('forumwise.admin_layout.scripts_section') : 'scripts' ;
    }
    if(auth()?->user()?->role == config('forumwise.db.roles.moderator') || auth()?->user()?->role == config('forumwise.db.roles.participant')){
         $extends = config('forumwise.user_layout.layout')  ? config('forumwise.user_layout.layout') : 'forumwise::layouts.form-wise' ;
         $content = config('forumwise.user_layout.content_section')  ? config('forumwise.user_layout.content_section') : 'content' ;
         $styles = config('forumwise.user_layout.style_section')  ? config('forumwise.user_layout.style_section') : 'styles' ;
         $scripts = config('forumwise.user_layout.scripts_section')  ? config('forumwise.user_layout.scripts_section') : 'scripts' ;
    }
    if(!auth()->check()){
        $extends ='forumwise::layouts.form-wise' ;
        $content = 'content' ;
        $styles = 'styles' ;
        $scripts = 'scripts' ;
    }   
@endphp
@extends($extends)
@section($content)  
<main class="fw-main-explore">
    <div class="fw-banner">
        <div class="fw-banner-content-main">
            <ul class="fw-forum_breadcrumb">
                <li class="fw-breadcrum-icon">
                    <a href="{{ route('forums') }}">
                        <i class="fw-icon-home-01-2"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('forum-topics', $topic->forum->slug) }}">
                    {{ __('forumwise::forum_wise.topics') }}
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="active">
                        {{ $topic->title }}
                    </a>
                </li>
            </ul>
            <div class="fw-banner-main">
                <div class="fw-banner-content">
                    @if($topic->media->count() > 0)
                        <img src="{{ url(Storage::url($topic->media->first()->path)) }}" alt="{{$topic->title}}">
                    @else
                        <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.placeholder_image') }}">    
                    @endif
                    <div class="fw-banner-title">
                        <h2>{{ $topic->title }}
                        @if($topic?->type == 'private')
                            <span class="fw-forum-stats"><i class="fw-icon-lock-close"></i></span>
                        @endif
                        </h2>   
                        <span>{{ $topic->description }}</span>
                    </div>  
                </div>
                <div x-data="{ textToCopy: '{{ route('topic', $topic?->slug) }}', copied: false }" class="fw-tab-content">
                    <div class="fw-searhcoption_btn">
                        <a href="javascript:void(0)" class="fw-active" id="show-votes">{{ $topic->votes_count }}</a>
                        <a href="javascript:void(0)" id="add-vote">{{ __('forumwise::forum_wise.vote') }}</a>
                    </div>
                    @if(auth()?->user()?->id == $topic?->created_by || auth()?->user()?->role == 'admin')
                        @if($topic?->type == 'private')
                            <a href="javascript:void(0)" class="fw-bookmark fw-invite" id="fw-invite"><i class="fw-icon-send-01"></i> {{ __('forumwise::forum_wise.send_invite') }}</a>
                        @else
                            <template x-if="!copied">
                                <div class="fw-bookmark fw-invite" @click="copied = true; navigator.clipboard.writeText(textToCopy); setTimeout(() => copied = false, 2000)">{{ __('forumwise::forum_wise.copy_link') }}
                                    <i class="fw-icon-copy-01"></i>
                                </div>
                            </template>
                            <template x-if="copied">
                                <span class="fw-bookmark fw-invite" x-show="copied" x-transition>{{ __('general.copied') }}</span>
                            </template>
                        @endif
                    @endif
                </div>
            </div>
            <div class="fw-author-main">
                <div class="fw-author">
                    <span><i class="fw-icon-user-01"></i></span>
                    <div class="fw-author-content">
                        <span>{{ __('forumwise::forum_wise.author') }}</span>
                        @if(config('forumwise.userinfo_relation'))
                            <em>{{ $topic->creator->profile->first_name }} {{ $topic->creator->profile->last_name }}</em>
                        @else
                            <em>{{ $topic->creator->first_name }} {{ $topic->creator->last_name }}</em>
                        @endif
                    </div>
                </div>
                <div class="fw-author fw-view">
                    <span><i class="fw-icon-eye-open-01"></i></span>
                    <div class="fw-author-content">
                        <span>{{ __('forumwise::forum_wise.total_views') }}</span>
                            @php
                            $viewsCount = $topic->views_count;
                            if ($viewsCount >= 1000) {
                                $formattedCount = number_format($viewsCount / 1000, 1) . 'k';
                            } else {
                                $formattedCount = $viewsCount;
                            }
                        @endphp
                        <em>{{ $formattedCount }}</em>
                    </div>
                </div>
                <div class="fw-author fw-reply">
                    <span><i class="fw-icon-arrow-corner-cw-lt"></i></span>
                    <div class="fw-author-content">
                        <span>{{ __('forumwise::forum_wise.total_replies') }}</span>
                        <em>{{ $topic->comments_count }}</em>
                    </div>
                </div>
                <div class="fw-author fw-post">
                    <span><i class="fw-icon-layer-01"></i></span>
                    <div class="fw-author-content">
                        <span>{{ __('forumwise::forum_wise.total_posts') }}</span>
                        <em>{{ $topic->posts->count() }}</em>
                    </div>
                </div>
                <div class="fw-author fw-activity">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="18" viewBox="0 0 19 18" fill="none">
                            <path d="M9.6001 6V9L11.8501 10.5M9.6001 16.5C5.45796 16.5 2.1001 13.1421 2.1001 9C2.1001 4.85786 5.45796 1.5 9.6001 1.5C13.7422 1.5 17.1001 4.85786 17.1001 9C17.1001 13.1421 13.7422 16.5 9.6001 16.5Z" stroke="#FD306E" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="fw-author-content">
                        <span>{{ __('forumwise::forum_wise.last_activity') }}</span>
                        <em>{{ $topic->updated_at->format('M d, Y') }}</em>
                    </div>
                </div>
            </div>
        </div>
     @include('forumwise::components.contributors')
    </div>
    @livewire('forumwise::forum-post',['slug' => $slug, 'topic' => $topic])
</main>   
@endsection
@push($styles)
    <link rel="stylesheet" href="{{ asset('modules/forumwise/css/icomoon/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/forumwise/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/forumwise/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/forumwise/css/icomoon/select2.min.css') }}"> 
    @if(config('forumwise.livewire'))
        @livewireStyles
    @endif
@endpush


@push($scripts)
    @if(config('forumwise.use_jquery'))
        <script src="{{ asset('modules/forumwise/js/jquery.min.js') }}"></script>
    @endif
    @if(config('forumwise.use_select2'))
        <script src="{{ asset('modules/forumwise/js/select2.min.js') }}"></script>
    @endif
    @if(config('forumwise.livewire'))
        @livewireScripts
    @endif
    <script defer src="{{ asset('modules/forumwise/summernote/summernote-lite.min.js') }}"></script>
    <script defer src="{{ asset('modules/forumwise/js/main.js') }}"></script>
@endpush


@push($scripts)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        jQuery(document).on('click', '#add-vote', function (e){
            Livewire.dispatch('add-vote');
        });
        jQuery(document).on('click', '#fw-invite', function (e){
            Livewire.dispatch('openInviteUserPopup');
        });
        jQuery(document).on('click', '#fw-contributor-img', function (e){
            Livewire.dispatch('openContributorPopup');
        });
        Livewire.on('show-votes', function (votes_count) {
            jQuery('#show-votes').text(votes_count);
        }); 

});
</script>
@endpush
