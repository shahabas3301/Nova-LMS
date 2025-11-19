<?php

namespace Modules\Forumwise\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Forumwise\Models\Forum;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('forumwise.db.prefix') . 'categories';
    }

    public function forums() {
        return $this->hasMany(Forum::class, 'category_id', 'id');
    }
}
