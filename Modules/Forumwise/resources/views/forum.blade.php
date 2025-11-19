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
    <div class="fw-forumwrap">
        <div class="fw-forum_banner">
            <div class="fw-forum_banner_bg">
                <span class="fw-banner_shapeone"></span>
                <span class="fw-banner_shapetwo"></span>
                @if(!empty(setting('_forum_wise.fw_right_shape_image')[0]['path']))
                    <img src="{{Storage::url(setting('_forum_wise.fw_right_shape_image')[0]['path'])}}" class="am-bgimg2" alt="{{ __('forumwise::forum_wise.image') }}">
                @endif
                @if(!empty(setting('_forum_wise.fw_shape_image')[0]['path']))
                    <img class="fw-banner_imgone" src="{{ Storage::url(setting('_forum_wise.fw_shape_image')[0]['path']) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                @endif
                @if(!empty(setting('_forum_wise.fw_left_shape_image')[0]['path']))
                    <img class="fw-banner_imgtwo" src="{{ Storage::url(setting('_forum_wise.fw_left_shape_image')[0]['path']) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                @endif
                @if(!empty(setting('_forum_wise.fw_right_shape_image')[0]['path']))
                    <img class="fw-banner_imgthree" src="{{ Storage::url(setting('_forum_wise.fw_right_shape_image')[0]['path']) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                @endif  
            </div>
            <div class="fw-forum_banner_title">
            @if(!empty(setting('_forum_wise.fw_heading')))
                <h1>{!! setting('_forum_wise.fw_heading') !!}</h1>
            @endif
            @if(!empty(setting('_forum_wise.fw_paragraph')))
                <p>{!! setting('_forum_wise.fw_paragraph') !!}</p>
            @endif
            </div>
            <div class="fw-themeform fw-searchform">
                <fieldset>
                    <div class="form-group">
                        <i class="fw-icon-search-02"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="{{ __('forumwise::forum_wise.search_discussions') }}">
                        @if(!empty(setting('_forum_wise.fw_btn_txt')))
                            <button id="searchButton" type="button" class="fw-btn">{{ setting('_forum_wise.fw_btn_txt') }}</button>
                        @endif
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="fw-forum_content">
             @livewire('forumwise::forum-wise')
            @include('forumwise::components.formWiseSidebar')
        </div>
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
        jQuery(document).on('change', '#searchInput', function (e){
            let value = e.target.value; 
            Livewire.dispatch('filterSearch', {search: value});
     });
});
</script>
@endpush
