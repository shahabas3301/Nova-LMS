<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ __('laraguppy::chatapp.app_title') }}{{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>      
        @stack('styles')
        @livewireStyles
    </head>
    <body>
        <main>
            <div class="container">
                @yield('content')
            </div>
        </main>
        @include('forumwise::components.popups')
        @livewireScripts
        <script src="{{ asset('modules/forumwise/js/jquery.min.js') }}"></script>
        <script src="{{ asset('modules/forumwise/js/select2.min.js') }}"></script>
        @stack('scripts')    
    </body>
</html>

