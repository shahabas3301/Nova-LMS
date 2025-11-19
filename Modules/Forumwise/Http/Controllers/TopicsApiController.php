<?php

namespace Modules\Forumwise\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Forumwise\Services\ForumTopicService;
use Modules\Forumwise\Transformers\TopicsResource;
use Modules\Forumwise\Transformers\PopularTopicsResource;
use Modules\Forumwise\Transformers\TopUsersResource;
use Modules\Forumwise\Transformers\RelatedTopicsResource;
use Modules\Forumwise\Http\Requests\TopicRequest;
use Modules\Forumwise\Http\Requests\ReplyRequest;
use Modules\Forumwise\Http\Requests\VoteRequest;
use Modules\Forumwise\Transformers\PopularTopicMediaResource;
use Modules\Forumwise\Transformers\TopUserMediaResource;
use Modules\Forumwise\Transformers\TopicDetailResource;
use Modules\Forumwise\Transformers\TopicContributorsResource;
use Modules\Forumwise\Transformers\CommentsResource;
use Modules\Forumwise\Services\ForumPostServices;
use Modules\Forumwise\Models\Topic;
use Illuminate\Support\Str;

class TopicsApiController extends Controller
{
    private $user;
    private $forumTopicService;
    private $forumPostServices;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);

        $topics = $this->forumTopicService->getTopicList($request->title, $request->slug, $request->filterType, $request->sortby ?? 'asc');
        if($topics->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.topics_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.topics_fetched_successfully'), 'data' => TopicsResource::collection($topics)]);

    }

    public function getTopicDetail($slug)
    {
        if(!$slug){
            return response()->json(['status' => 404, 'message' => 'Topic not found', 'data' => []]);
        }
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $topic = $this->forumTopicService->getTopicDetail($slug);
        if(!$topic){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.topics_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.topic_fetched_successfully'), 'data' => TopicDetailResource::make($topic)]);
    }

    public function getPopularTopics()
    {
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $popularTopics = $this->forumTopicService->getPopularTopics();
    
        if($popularTopics->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.popular_topics_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.popular_topics_fetched_successfully'), 'data' => PopularTopicsResource::collection($popularTopics)]);
    }

    public function getPopularTopicsMedia()
    {
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $popularTopicsMedia = $this->forumTopicService->getPopularTopicsMedia();
        if($popularTopicsMedia->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.popular_topics_media_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.popular_topics_media_fetched_successfully'), 'data' => PopularTopicMediaResource::collection($popularTopicsMedia)]);
    }

    public function getTopicContributors($slug)
    {
        if(!$slug){
            return response()->json(['status' => 404, 'message' => 'Topic not found', 'data' => []]);
        }
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $topicContributors = $this->forumTopicService->getTopicContributors($slug);
        if($topicContributors->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.topic_contributors_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.topic_contributors_fetched_successfully'), 'data' => TopicContributorsResource::collection($topicContributors)]);
    }
    
    public function getRelatedTopic($slug)
    {
        if(!$slug){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.related_topics_not_found'), 'data' => []]);
        } else {
            $topic = Topic::where('slug', $slug)->first();
            if(!$topic){
                return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.related_topics_not_found'), 'data' => []]);
            }
        }
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $relatedTopics = $this->forumTopicService->getRelatedTopic($topic);
        if($relatedTopics->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.related_topics_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.related_topics_fetched_successfully'), 'data' => RelatedTopicsResource::collection($relatedTopics)]);
    }

    public function getTopUserMedia()
    {
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $topUserMedia = $this->forumTopicService->getTopUserMedia();
        if($topUserMedia->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.top_user_media_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.top_user_media_fetched_successfully'), 'data' => TopUserMediaResource::collection($topUserMedia)]);
    }

    public function addReply(ReplyRequest $request)
    {
        $response = isDemoSite();
        if( $response ){
            return $this->error(data: null,message: __('general.demosite_res_txt'),code: Response::HTTP_FORBIDDEN);
        }
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $reply = $this->forumTopicService->addReply($data);
        
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.reply_added_successfully'), 'data' => $reply]);
    }

    public function addVote(VoteRequest $request)
    {
        $response = isDemoSite();
        if( $response ){
            return $this->error(data: null,message: __('general.demosite_res_txt'),code: Response::HTTP_FORBIDDEN);
        }
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $vote = $this->forumTopicService->addVote($data);
        if( $vote['already_voted'] == 'already_voted' ){
            return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.already_voted')]);
        } else {
            return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.voted_successfully')]);
        }
        
    }

    public function getComments($topicId)
    {
        if(!$topicId){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.topics_not_found'), 'data' => []]);
        }
        $this->user = Auth::user();  
        $this->forumPostServices = new ForumPostServices();
        $comments = $this->forumPostServices->getPost($topicId);
      
        if($comments->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.comments_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.comments_fetched_successfully'), 'data' => CommentsResource::collection($comments)]);
    }

    public function getTopUsers()
    {
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);
        $topUsers = $this->forumTopicService->getTopUsers();
        if($topUsers->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.top_users_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.top_users_fetched_successfully'), 'data' => TopUsersResource::collection($topUsers)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createTopic(TopicRequest $request)
    {
        $response = isDemoSite();
        if( $response ){
            return $this->error(data: null,message: __('general.demosite_res_txt'),code: Response::HTTP_FORBIDDEN);
        }
        $data = $request->validated();
        $this->user = Auth::user();  
        $this->forumTopicService = new ForumTopicService($this->user);

        $image = $data['image'] ?? null;

        if ($image && $image instanceof \Illuminate\Http\UploadedFile) {
            $randomNumber = Str::random(40);
            $imageName = $randomNumber . '_' . $image->getClientOriginalName();
            $filePath = 'topic/' . $imageName;
            $image = Storage::disk(getStorageDisk())->put($filePath, file_get_contents($image));
        }
        unset($data['image']);
        $data['created_by'] = $this->user->id; 
        $topicinfo = $this->forumTopicService->storeOrUpdateTopicDetail($data);
        $media = $this->forumTopicService->addTopicMedia($topicinfo, $filePath);
        $this->forumTopicService->addTopicUser($topicinfo->id, $this->user->id);
 
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.topic_created_successfully')]);
    }
}
