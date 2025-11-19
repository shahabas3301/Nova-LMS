<?php

namespace Modules\Forumwise\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Forumwise\Models\Forum;
use Modules\Forumwise\Models\Topic;
use Modules\Forumwise\Models\TopicUser;
use Illuminate\Support\Facades\Auth;
use Modules\Forumwise\Models\Comment;
use Modules\Forumwise\Services\ForumTopicService;
use Illuminate\Support\Facades\DB;

class ForumwiseController extends Controller
{

    public function index()
    {
        $totalTopics    = Topic::withWhereHas('forum.category')->count();
        $totalPosts     = Comment::where('parent_id', null)->withWhereHas('topic.forum.category')->count();
        $totalForums    = Forum::withWhereHas('category')->count();
        $totalMembers   = TopicUser::withWhereHas('topic.forum.category')->count();
        return view('forumwise::forum',compact('totalTopics', 'totalPosts', 'totalForums', 'totalMembers'));
    }
    
    public function fetchTopic($slug)
    {
        $forum = Forum::where('slug', $slug)->first();
        $roles = json_decode($forum?->topic_role, true) ?? [];
        return view('forumwise::topic', compact('slug', 'roles', 'forum'));
    }

    public function fetchPost($slug)
    {
        $profileRelation = config('forumwise.userinfo_relation');
        $userFirstNameCol = config('forumwise.user_first_name_column');
        $userLastNameCol = config('forumwise.user_last_name_column');
        $userImageCol = config('forumwise.user_image_column');
        $forumTopicService = new ForumTopicService(Auth::user());
        $topic = $forumTopicService->getTopicDetail($slug);
        if(!$topic) {
            return redirect()->route('forums');
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
        return view('forumwise::post',compact('slug', 'contributors', 'topic'));
    }

    public function categories()
    {
        return view('forumwise::category');
    }
}
