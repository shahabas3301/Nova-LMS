<?php

namespace Modules\Starup\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeRule extends Model
{

    protected $fillable = ['badge_id', 'criterion_type', 'criterion_name', 'criterion_value'];

    /**
     * Get the badge for the rule.
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
        return config('starup.db_prefix') . 'badge_rules';
    }
}
