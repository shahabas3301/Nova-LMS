@php
    if(auth()->user()->role == config('forumwise.db.roles.administrator')){
         $extends = config('forumwise.admin_layout.layout')  ? config('forumwise.admin_layout.layout') : 'forumwise::layouts.form-wise' ;
         $content = config('forumwise.admin_layout.content_section')  ? config('forumwise.admin_layout.content_section') : 'content' ;
         $styles = config('forumwise.admin_layout.style_section')  ? config('forumwise.admin_layout.style_section') : 'styles' ;
         $scripts = config('forumwise.admin_layout.scripts_section')  ? config('forumwise.admin_layout.scripts_section') : 'scripts' ;
    }
    if(auth()->user()->role == config('forumwise.db.roles.moderator') || auth()->user()->role == config('forumwise.db.roles.participant')){
         $extends = config('forumwise.user_layout.layout')  ? config('forumwise.user_layout.layout') : 'forumwise::layouts.form-wise' ;
         $content = config('forumwise.user_layout.content_section')  ? config('forumwise.user_layout.content_section') : 'content' ;
         $styles = config('forumwise.user_layout.style_section')  ? config('forumwise.user_layout.style_section') : 'styles' ;
         $scripts = config('forumwise.user_layout.scripts_section')  ? config('forumwise.user_layout.scripts_section') : 'scripts' ;
    }
@endphp
@extends($extends)
@section($content)
<main class="fw-main">
    <div class="fw-forumwrap fw-forum-topic-wrapper">
        <div class="fw-forum_banner">
            <div class="fw-forum_banner_bg">
                <span class="fw-banner_shapeone"></span>
                <span class="fw-banner_shapetwo"></span>
                @if(!empty(setting('_forum_wise.fw_shape_image')[0]['path']))
                    <img class="fw-banner_imgone" src="{{ Storage::url(setting('_forum_wise.fw_shape_image')[0]['path']) }}" alt="{{ __('Image') }}" />
                @endif
                @if(!empty(setting('_forum_wise.fw_left_shape_image')[0]['path']))
                    <img class="fw-banner_imgtwo" src="{{ Storage::url(setting('_forum_wise.fw_left_shape_image')[0]['path']) }}" alt="{{ __('Image') }}" />
                @endif
                @if(!empty(setting('_forum_wise.fw_right_shape_image')[0]['path']))
                    <img class="fw-banner_imgthree" src="{{ Storage::url(setting('_forum_wise.fw_right_shape_image')[0]['path']) }}" alt="{{ __('Image') }}" />
                @endif  
            </div>
            
            <ul class="fw-forum_breadcrumb">
                <li>
                    <a href="{{ route('forums') }}">
                        <i class="fw-icon-home-01-2"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="active">
                       {{ $forum?->title }}
                    </a>
                </li>
            </ul>
            <div class="fw-forum_banner_title">
                <h1>{{ $forum?->title }}</h1>
                <p>{{ $forum?->description }}</p>
            </div>
            @if(in_array(auth()?->user()?->role, $roles) || auth()?->user()?->role == config('forumwise.db.roles.administrator') )
                 <a href="javascript:void(0);" id="fw-opentopic-popup" fw-data-target="#fw-addforumpopup" class="fw-danger"><i class="fw-icon-layer-01"></i>  {{ __('forumwise::forum_wise.create_a_new_topic') }} </a>
            @endif
        </div>
        @livewire('forumwise::forum-topic',['slug' => $slug , 'roles' => $roles])
    </div>
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
<script defer src="{{ asset('modules/forumwise/js/main.js') }}"></script>
@endpush
@push($scripts)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        jQuery(document).on('click', '#fw-opentopic-popup', function (e){
            Livewire.dispatch('openAddTopicPopup');
     });
});
</script>
@endpush
