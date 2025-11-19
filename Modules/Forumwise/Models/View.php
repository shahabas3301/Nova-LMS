<?php

namespace Modules\Forumwise\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
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
        return config('forumwise.db.prefix') . 'views';
    }
    
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}       