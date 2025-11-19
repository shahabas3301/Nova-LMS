<?php

namespace Modules\Forumwise\Services;
use Modules\Forumwise\Models\Category;

class CategoryServices
{
    public function getCategories($search, $sortby, $per_page)
    {
        $categories = Category::select('id', 'name', 'slug','label_color')
            ->when($search, function($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->orderBy('id', $sortby)
        ->paginate($per_page);
        return $categories;
    }
}