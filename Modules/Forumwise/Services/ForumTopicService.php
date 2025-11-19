<?php

namespace Modules\Forumwise\Services;

use Modules\Forumwise\Models\Topic;
use Illuminate\Support\Facades\DB;
use Modules\Forumwise\Models\TopicUser;
use App\Models\User;
use Modules\Forumwise\Services\ForumPostServices;
use Illuminate\Support\Str;

class ForumTopicService {

    public $user;
    public $forumPostServices;

    public function __construct($user) {
        $this->user = $user;
        $this->forumPostServices = new ForumPostServices();
    }

    private function addCreatorRelation($query)
    {
        $profileRelation = config('forumwise.userinfo_relation');
        $userFirstNameCol = config('forumwise.user_first_name_column');
        $userLastNameCol = config('forumwise.user_last_name_column');
        $userImageCol = config('forumwise.user_image_column');

        $creatorFields = ['id', $userFirstNameCol, $userLastNameCol, $userImageCol];

        if ($profileRelation) {
            $query->with([
                'creator.' . $profileRelation => function ($q) use ($creatorFields) {
                    $q->select(array_merge($creatorFields, ['user_id']));
                },
            ]);
        } else {
            $query->with([
                'creator' => function ($q) use ($creatorFields) {
                    $q->select($creatorFields);
                },
            ]);
        }

        return $query;
    }

    public function getTopicDetail($slug)
    {
        $query = Topic::where('slug', $slug)
            ->with('posts')
            ->withCount(['comments', 'posts', 'views', 'votes'])
            ->with('media');

        return $this->addCreatorRelation($query)->first();
    }

    public function getTopicContributors($slug)
    {
        $profileRelation = config('forumwise.userinfo_relation');
        $userFirstNameCol = config('forumwise.user_first_name_column');
        $userLastNameCol = config('forumwise.user_last_name_column');
        $userImageCol = config('forumwise.user_image_column');
        $topic = $this->getTopicDetail($slug);
        if(!$topic) {
            return [];
        }
        $contributors = TopicUser::select('user_id', \DB::raw('MAX(status) as status')) 
        ->where('topic_id', $topic->id)
        ->where(function($query) {
            $query->whereNull('status')
                  ->orWhere('status', 3);
        })
        ->groupBy('user_id')
        ->when($profileRelation, function ($query) use ($profileRelation, $userFirstNameCol, $userLastNameCol, $userImageCol) {
            $query->with([
                'users.' . $profileRelation => function ($q) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                    $q->select('id', 'user_id', $userFirstNameCol, $userLastNameCol, $userImageCol);
                },
            ]);
        }, function ($query) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
            $query->with([
                'users' => function ($q) use ($userFirstNameCol, $userLastNameCol, $userImageCol) {
                    $q->select('id', $userFirstNameCol, $userLastNameCol, $userImageCol);
                },
            ]);
        })->get();
        return $contributors;
    }

    public function getTopicList($search = "", $slug = "", $filterType = 'all', $sortby = 'desc')
    {
        $topics = Topic::select('id', 'title', 'slug', 'description', 'status', 'forum_id', 'updated_at', 'type', 'created_by')
            ->when($filterType == 'my', function ($query) {
                $query->where('created_by', $this?->user?->id);
            })
            ->withCount(['posts', 'comments', 'views'])
            ->with('media');

        $this->addCreatorRelation($topics);

        if (!empty($search)) {
           
            $topics->where(function ($query) use ($search) {
                $query->whereFullText('title', $search);
            });
        }
        
        if (!empty($slug)) {
            $topics->whereHas('forum', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        }

        $topics = $topics->orderBy('id', $sortby)->get();
        return $topics;
    }

    public function getPopularTopicsMedia()
    {
        return Topic::withCount('posts')
        ->with('media')
        ->having('posts_count', '>', 0)
        ->get();
    }

    public function getPopularTopics($limit = 4)
    {
        $topics = Topic::withCount('posts')
            ->with('media')
            ->having('posts_count', '>', 0);
        
        $this->addCreatorRelation($topics);

        return $topics->orderBy('posts_count', 'desc')->withCount('posts')->limit($limit)->get();
    }

    public function storeOrUpdateTopicDetail($data, $formId = null) {
        
        return Topic::updateOrCreate(['id'=>$formId], $data);
    }

    public function addTopicMedia($topic, $image)
    {   
        if ($image) {
            return $topic->media()->updateOrCreate(
                ['type' => 'image'],
                ['path' => $image]
            );
        }
        return null;
    }

    public function addTopicUser($topicId, $userId) {
        $existingUser = TopicUser::where('topic_id', $topicId)->where('user_id', $userId)->first();
        if (!$existingUser) {
            return TopicUser::create(['topic_id' => $topicId, 'user_id' => $userId]);
        }
        return null;
    }

    public function addReply($data) {
        $postDetail = [
            'topic_id'                  => $data['topic_id'],
            'description'               => $data['description'],
            'created_by'                => $data['user_id'],
        ];

        $post = $this->forumPostServices->createPost($postDetail);

        $this->forumPostServices->addTopicUser($data['topic_id'], $data['user_id']);
        $topic = Topic::find($data['topic_id']);
        $topic->updated_at = now();
        $topic->save();
        return $post;
    }

    public function addVote($data) {
  
        $topic = Topic::findOrFail($data['topic_id']);
        $existingVote = $topic->votes()->where('user_id', $data['user_id'])->first();
        if ($existingVote) {
            $topic['already_voted'] = 'already_voted';
            return $topic;
        }
        $vote = $topic->votes()->create([
            'user_id'   => $data['user_id'],
            'type'      => 'vote',
        ]);
  
        $this->forumPostServices->addTopicUser($topic->id, $data['user_id']);
        $topic->updated_at = now();
        $topic->save();
        return $vote;
    }

    public function getRelatedTopic($topic)
    {
        $query = Topic::where('forum_id', $topic->forum_id)->withCount('posts')
            ->with('media')
            ->where('id', '!=', $topic->id)
            ->limit(4);

        return $this->addCreatorRelation($query)->get();
    }
    
    public function getTopUserMedia() {
        return User::select('users.id', DB::raw("CONCAT(profiles.first_name, ' ', profiles.last_name) as name"), 'profiles.image', DB::raw('COUNT(DISTINCT fw__topics.id) as topic_count'), DB::raw('COUNT(DISTINCT posts.id) as post_count'), DB::raw('COUNT(DISTINCT fw__topics.id) + COUNT(DISTINCT posts.id) as total_count'))
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->leftJoin('fw__topics', 'fw__topics.created_by', '=', 'users.id')
            ->leftJoin('fw__comments as posts', function($join) {
                $join->on('posts.created_by', '=', 'users.id')
                    ->whereNull('posts.parent_id');
            })
            ->groupBy('users.id', 'profiles.first_name', 'profiles.last_name', 'profiles.image')
            ->having('total_count', '>', 0)
            ->orderBy('total_count', 'desc')
            ->limit(4)
            ->get();
    }
    
    public function getTopUsers() {
        $topUsers = User::select('users.id', DB::raw("CONCAT(profiles.first_name, ' ', profiles.last_name) as name"), 'profiles.image', DB::raw('COUNT(DISTINCT fw__topics.id) as topic_count'), DB::raw('COUNT(DISTINCT posts.id) as post_count'), DB::raw('COUNT(DISTINCT fw__topics.id) + COUNT(DISTINCT posts.id) as total_count'))
        ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
        ->leftJoin('fw__topics', 'fw__topics.created_by', '=', 'users.id')
        ->leftJoin('fw__comments as posts', function($join) {
            $join->on('posts.created_by', '=', 'users.id')
                 ->whereNull('posts.parent_id');
        })
        ->groupBy('users.id', 'profiles.first_name', 'profiles.last_name', 'profiles.image')
        ->having('total_count', '>', 0)
        ->orderBy('total_count', 'desc')
        ->limit(4)
        ->get();
        return $topUsers;
    }
    
}
