<?php

namespace Modules\Starup\Models;

use Modules\Starup\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    protected $fillable = ['user_id', 'badge_id'];

    /**
     * Get the user for the user badge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the badge for the user badge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Get the table name for the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('starup.db_prefix') . 'user_badges';
    }
}
