<?php

namespace Modules\Forumwise\Observers;

use Modules\Forumwise\Models\Forum;
use Illuminate\Support\Str;

class ForumObserver
{
    /**
     * Handle the Course "creating" event.
     *
     * @param  \Modules\Forumwise\Models\Forum  $forum
     * @return void
     */
    
    public function created(Forum $forum)
    {
        $slug            = Str::slug($forum->title);
        $forum->slug    = $this->uniqueSlug($slug, $forum);

        $forum->save();
    }

    /**
     * Create unique slug automatically
     * @return string unique slug
     */

    protected function uniqueSlug($slug, $forum)
    {
        if (Forum::whereSlug($slug)->whereNot('id', $forum->id)->exists()) {
            $slug = $slug . "-" . $forum->id;
        }
        return $slug;
    }
}
