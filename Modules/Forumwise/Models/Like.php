<?php

namespace Modules\Forumwise\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Forumwise\Casts\LikeTypeCast;

class Like extends Model {
    use HasFactory;

    protected $guarded = [];

   /**
     * Get the table associated with the model.
     *
     * @return string
    */

    public function getTable()
    {
        return config('forumwise.db.prefix') . 'likes';
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'likeable_type' => 'string',
        'type' => LikeTypeCast::class,
    ];

    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

}
