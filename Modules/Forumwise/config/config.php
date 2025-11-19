<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    
    'route' => [
        'prefix'        => '',
        'middlewares'   => '',
    ],

    'db' => [
        'prefix'    => 'fw__',
        'roles'     =>[
            'administrator'     => 'admin', 
            'moderator'         => 'tutor', 
            'participant'       => 'student', 
        ]
    ],

    'admin_layout'  => [
        'layout'             => 'layouts.admin-app',
        'content_section'    => '',
        'scripts_section'    => '',
        'style_section'      => ''
    ],

    'user_layout' => [
        'layout' => 'layouts.app',
        'content_section' => '',
        'scripts_section' => '',
        'style_section' => ''
    ],    
    'image' => [
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'max_size' => 2048, 
    ],
    
    'user_class'                => 'App\Models\User',
    'user_table'                => 'users',

    'use_jquery' => false,
    'use_bootstrap' => false,
    'use_select2' => false,
    'livewire' => false,
    'user_first_name_column'    => 'first_name',
    'user_last_name_column'     => 'last_name',
    'user_image_column'         => 'image',
    'userinfo_relation'         => 'profile',
    'user_email_column'         =>  'email'

];
