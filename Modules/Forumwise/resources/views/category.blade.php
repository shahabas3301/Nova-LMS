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
    @livewire('forumwise::forum-category')
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
