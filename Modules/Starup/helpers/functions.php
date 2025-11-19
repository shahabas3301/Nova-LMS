<?php


if (!function_exists('badgeMenuOptions')) {
    function badgeMenuOptions()
    {
        return [
            [
                'title' =>  __('starup::starup.badges'),
                'icon'  => 'icon-award',
                'routes' => [
                    'badges.badge-list' => __('starup::starup.badges'),
                ],
            ],
        ];
    }
}

if (!function_exists('getStarupDbPrefix')) {
    function getStarupDbPrefix()
    {
        return config('starup.db_prefix');
    }
}
