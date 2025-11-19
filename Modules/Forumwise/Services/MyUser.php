<?php

namespace Modules\Forumwise\Services;
use Illuminate\Support\Facades\Storage;

class MyUser {

    public function extractUserInfo($user) {
        $userName = $userPhoto = $userId = null;

        $userFirstNameCol = (string)config('forumwise.user_first_name_column');
        $userLastNameCol  = (string)config('forumwise.user_last_name_column');
        $userImageCol   = (string)config('forumwise.user_image_column');

        if (!empty($user)) {
            $userId = $user->id;
            $profile = $this->getActiveProfile($user);

            if ( (!empty($userFirstNameCol) || !empty($userLastNameCol) ) && !empty($profile)) {
                $userName = trim(($profile->$userFirstNameCol ?? null ).' '.($profile->$userLastNameCol));
            } elseif (!empty($userFirstNameCol) || !empty($userLastNameCol)) {
                $userName = trim(($user->$userFirstNameCol ?? null ).' '.($user->$userLastNameCol));
            } else {
                $userName = $user->name ?? null;
            }
            if (!empty($userImageCol) && !empty($profile)) {
                $userPhoto = $profile->$userImageCol ?? null;
            } elseif (!empty($userImageCol)) {
                $userPhoto = $user->$userImageCol ?? null;
            } else {
                $userPhoto = null;
            }
        }
        return [
            'name'      => $userName,
            'userId'    => $userId,
            'photo'     => !empty($userPhoto) ? Storage::url($userPhoto) : null,
        ];
    }

    public function getActiveProfile($user) {
        $profileRelation = config('forumwise.userinfo_relation');
        if (!empty($profileRelation) && !empty($user->{$profileRelation})) {
            return $user->{$profileRelation};
        }
    }
}
