<?php

namespace Modules\Forumwise\Models;
use Modules\Forumwise\Casts\ForumStatusCast;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Forumwise\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\Forumwise\Models\Category;


class Forum extends Model
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
        return config('forumwise.db.prefix') . 'forums';
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ForumStatusCast::class,
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }

        /**
     * Scope a query to only include inactive users.
     */
    public function scopeClosed(Builder $query): void
    {
        $query->where('status', 0);
    }
    
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Topic::class)->where('parent_id', null);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function topicUsers(): HasManyThrough {
        return $this->hasManyThrough(TopicUser::class, Topic::class);
    }
}
