<?php

namespace Modules\Forumwise\Services;

use Modules\Forumwise\Models\Forum;
use Modules\Forumwise\Models\ForumUser;
use Modules\Forumwise\Models\Category;

class ForumWiseService {

    public $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function getForumList($search = "", $sortby = 'asc') {
        $userFirstNameCol   = (string)config('forumwise.user_first_name_column');
        $userLastNameCol    = (string)config('forumwise.user_last_name_column');
        $userImageCol       = (string)config('forumwise.user_image_column');
        $profileRelation    = config('forumwise.userinfo_relation');
    
        $categories = Category::select('id', 'name', 'label_color')
            ->withWhereHas('forums', function ($query) use ($search, $profileRelation, $userFirstNameCol, $userLastNameCol, $userImageCol) {
                    $query->select('id', 'title', 'description', 'status', 'category_id', 'created_by', 'slug', 'topic_role')
                        ->withCount('topics')
                        ->withCount('posts')
                        ->with('media');
                    if (!empty($search)) {
                        $query->where(function ($sub_query) use ($search) {
                            $sub_query->whereFullText('title', $search)
                                ->orWhereFullText('description', $search);
                        });
                    }
        });
    
        $categories = $categories->orderBy('id', $sortby)->get();
        return $categories;
    }
    

    public function getCategoryList() {
        
        return Category::select('id', 'name')->get();
    }


    public function getForumBySlug($slug) {
        return Forum::where('slug', $slug)->first();
    }

    public function storeOrUpdateForumDetail($data, $formId) {
        return Forum::updateOrCreate(['id'=>$formId],$data);
    }

    public function addForumMedia($forum, $image)
    {   
        if ($image) {
            return $forum->media()->updateOrCreate(
                ['type' => 'image'],
                ['path' => $image]
            );
        }
        return null;
    }

}