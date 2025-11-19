<?php

namespace Modules\Forumwise\Services;

use Modules\Forumwise\Models\Comment;
use Modules\Forumwise\Models\TopicUser;
use Modules\Forumwise\Models\View;

class ForumPostServices
{
    public function createPost(array $data)
    {
        return Comment::create($data);
    }

    public function getPost($topicId)
    {
       
        $profileRelation = config('forumwise.userinfo_relation');
        $userFirstNameCol = config('forumwise.user_first_name_column');
        $userLastNameCol = config('forumwise.user_last_name_column');
        $userImageCol = config('forumwise.user_image_column');
        
        return Comment::where('topic_id', $topicId)
            ->whereNull('parent_id')
            ->withCount('likes')
            ->withCount('replies') 
            ->with(['likes' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->with(['replies' => function ($query) use ($profileRelation, $userFirstNameCol, $userLastNameCol, $userImageCol) {
                $query->withCount('replies') 
                    ->with(['replies' => function ($q) {
                        $q->with('replies'); 
                        $q->withCount('likes');
                        $q->withCount('replies');
                        $q->with(['likes' => function ($query) {
                            $query->where('user_id', auth()->id());
                        }]);
                    }])
                    ->withCount('likes')
                    ->with(['likes' => function ($query) {
                        $query->where('user_id', auth()->id());
                    }])
                    ->when($profileRelation, function ($q) use ($profileRelation, $userFirstNameCol, $userLastNameCol, $userImageCol) {
                        $q->with([
                            'creator.' . $profileRelation => function ($subQ) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                                $subQ->select('id', 'user_id', $userFirstNameCol, $userLastNameCol, $userImageCol);
                            },
                        ]);
                    }, function ($q) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                        $q->with([
                            'creator' => function ($subQ) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                                $subQ->select('id', $userFirstNameCol, $userLastNameCol, $userImageCol);
                            },
                        ]);
                    });
            }])
            ->when($profileRelation, function ($query) use ($profileRelation, $userFirstNameCol, $userLastNameCol, $userImageCol) {
                $query->with([
                    'creator.' . $profileRelation => function ($q) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                        $q->select('id', 'user_id', $userFirstNameCol, $userLastNameCol, $userImageCol);
                    },
                ]);
            }, function ($query) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                $query->with([
                    'creator' => function ($q) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                        $q->select('id', $userFirstNameCol, $userLastNameCol, $userImageCol);
                    },
                ]);
            })->orderBy('created_at', 'asc')->get();
    }

    public function addPostMedia($post, $image)
    {   
        if ($image) {
            return $post->media()->updateOrCreate(
                ['type' => 'image'],
                ['path' => $image]
            );
        }
        return null;
    }

    public function createView($topicId, $userId)
    {
        $existingView = View::where('topic_id', $topicId)->where('user_id', $userId)->first();
        if (!$existingView) {
            View::create([
                'topic_id' => $topicId,
                'user_id' => $userId,
            ]);
        }
    }

    public function addTopicUser($topicId, $userId) {
        $existingUser = TopicUser::where('topic_id', $topicId)->where('user_id', $userId)->where('status','3')->first();
        if (!$existingUser) {
            return TopicUser::create(['topic_id' => $topicId, 'user_id' => $userId, 'status' => 'contributor']);
        }
        return null;
    }

    public function getTopicUserStatus($topicId, $userId)
    {
        return TopicUser::where('topic_id', $topicId)->where('user_id', $userId)->where('status', '!=', null)->where('status', '!=', 3)->pluck('status')->first();
        
    }

    public function sendInvite($userId, $topicId)
    {
        $existingUser = TopicUser::where('topic_id', $topicId)
            ->where('user_id', $userId)
            ->whereIn('status', ['1', '2']) 
            ->first();
        if (!$existingUser) {
            TopicUser::create([
                'status' => 'invited',
                'topic_id' => $topicId,
                'user_id' => $userId,
            ]);
        }
    }

    public function acceptInvitation($topicId, $userId, $status)
    {
        $existingUser = TopicUser::where('topic_id', $topicId)->where('user_id', $userId)->where('status','1')->first();
        if ($existingUser) {
            $existingUser->status = $status;
            $existingUser->save();
        }
    }
    public function getInvitedUser($email)
    {
        return config('auth.providers.users.model')::where('email', $email)->first();
    }

    public function invitedUser($topicId, $userId)   
    {
        return TopicUser::where('topic_id', $topicId)->whereIn('status', [0, 1, 2])->where('user_id', $userId)->first();

    }

}