<?php

namespace Modules\Forumwise\Observers;

use Modules\Forumwise\Models\Topic;
use Illuminate\Support\Str;

class TopicObserver
{
    /**
     * Handle the Course "creating" event.
     *
     * @param  \Modules\Forumwise\Models\Topic  $topic
     * @return void
     */
    
    public function created(Topic $topic)
    {
        $slug            = Str::slug($topic->title);
        $topic->slug    = $this->uniqueSlug($slug, $topic);
        $topic->save();
    }

    /**
     * Create unique slug automatically
     * @return string unique slug
     */

    protected function uniqueSlug($slug, $topic)
    {
        if (Topic::whereSlug($slug)->whereNot('id', $topic->id)->exists()) {
            $slug = $slug . "-" . $topic->id;
        }
        return $slug;
    }
}
