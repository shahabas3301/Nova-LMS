<?php

namespace Modules\Starup\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BadgeCategory extends Model
{
    protected $fillable = ['id', 'name', 'slug', 'description'];

    /**
     * Get the badges for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class);
    }

    /**
     * Get the table name for the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('starup.db_prefix') . 'badge_categories';
    }
}
