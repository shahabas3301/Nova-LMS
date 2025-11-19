<?php

namespace Modules\Forumwise\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Forumwise\Models\Media;
use Modules\Forumwise\Models\Topic;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Comment extends Model
{
   
    protected $guarded = [];

    public function getTable()
    {
        return config('forumwise.db.prefix') . 'comments';
    }

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }


    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}

