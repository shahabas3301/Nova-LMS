<?php

namespace Modules\Forumwise\Observers;

use Modules\Forumwise\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Course "creating" event.
     *
     * @param  \Modules\Forumwise\Models\Category  $category
     * @return void
     */
    
    public function creating(Category $category)
    {
        $slug            = Str::slug($category->name);
        $category->slug    = $this->uniqueSlug($slug, $category);
    }

    /**
     * Create unique slug automatically
     * @return string unique slug
     */

    protected function uniqueSlug($slug, $category)
    {
        if (Category::withTrashed()->whereSlug($slug)->whereNot('id', $category->id)->exists()) {

            $slug = $slug . "-" . $category->id;
        }
        return $slug;
    }
}
