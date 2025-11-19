<?php

namespace Modules\Starup\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Badge extends Model
{

    protected $fillable = ['category_id', 'name', 'description', 'image'];

    /**
     * Get the category for the badge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BadgeCategory::class);
    }

    /**
     * Get the rules for the badge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules(): HasMany
    {
        return $this->hasMany(BadgeRule::class);
    }

    /**
     * Get the users for the badge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, config('starup.table_prefix'). 'user_badges', 'badge_id', 'user_id');
    }

    /**
     * Get the table name for the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('starup.db_prefix') . 'badges';
    }
    /**
     * Get the rules for the badge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function badgeRules(): HasMany
    {
        return $this->hasMany(BadgeRule::class);
    }
}
