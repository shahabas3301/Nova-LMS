<?php

namespace Modules\Forumwise\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Forumwise\Casts\UserTopicCast;

class TopicUser extends Model
{
    use HasFactory;

    protected $guarded = [];


    /**
     * Get the table associated with the model.
     *
     * @return string
    */

    protected function casts(): array
    {
        return [
            'status' => UserTopicCast::class,
        ];
    }

    public function getTable()
    {
        return config('forumwise.db.prefix') . 'topic_users';
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

  
    public function users()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
