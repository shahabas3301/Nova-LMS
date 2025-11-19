<?php

namespace Modules\Forumwise\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Forumwise\Services\CategoryServices;
use Modules\Forumwise\Transformers\CategoriesResource;

class ForumwiseApiController extends Controller
{
    protected $categoryServices;
    public function __construct(CategoryServices $categoryServices)
    {
        $this->categoryServices = $categoryServices;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->categoryServices->getCategories($request->search, $request->sortby, $request->per_page);
        if($categories->isEmpty()){
            return response()->json(['status' => 404, 'message' => __('forumwise::forum_wise.categories_not_found'), 'data' => []]);
        }
        return response()->json(['status' => 200, 'message' => __('forumwise::forum_wise.categories_fetched_successfully'), 'data' => CategoriesResource::collection($categories)]);
    }
}
