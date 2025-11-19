<?php

namespace Modules\Forumwise\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Forumwise\Services\ForumWiseService; 
use Modules\Forumwise\Transformers\ForumsResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ForumsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->user = Auth::user();  
        $this->forumWiseService = new ForumWiseService($this->user);
        $forums = $this->forumWiseService->getForumList($request->title ?? '', $request->sortby ?? 'asc');
        if($forums->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.forums_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.forums_fetched_successfully'), 'data' => ForumsResource::collection($forums)]);
    }
}
