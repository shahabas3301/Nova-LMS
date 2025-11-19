<?php

namespace Modules\Forumwise\Models;

use Modules\Forumwise\Casts\TopicTypeCast;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\Forumwise\Models\Comment;
use Modules\Forumwise\Models\Like;
use Modules\Forumwise\Models\View;
use Modules\Forumwise\Casts\ForumStatusCast;
use Modules\Forumwise\Models\Media;
class Topic extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the table associated with the model.
     *
     * @return string
    */

    public function getTable()
    {
        return config('forumwise.db.prefix') . 'topics';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status'    => ForumStatusCast::class,
            'type'      => TopicTypeCast::class,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
    ];

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function posts()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    
    public function media() 
    {
        return $this->morphMany(Media::class, 'mediaable');
    }
    
    public function topicUser()
    {
        return $this->hasMany(TopicUser::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class)->whereNotNull('parent_id');
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }


    public function votes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

}

