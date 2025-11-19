<?php

namespace Modules\Forumwise\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model {
    use HasFactory;

    protected $guarded = [];

   /**
     * Get the table associated with the model.
     *
     * @return string
    */

    public function getTable()
    {
        return config('forumwise.db.prefix') . 'media';
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
    ];
}
