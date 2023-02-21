<?php

namespace Lecturize\Followers\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Follower
 * @package Lecturize\Followers\Models
 */
class Follower extends Model
{
    // Laravel Packages
    use SoftDeletes;

    /** @inheritdoc */
    protected $fillable = [
        'follower_id',
        'follower_type',
        'followable_id',
        'followable_type',
    ];

    /** @inheritdoc */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /** @inheritdoc */
    protected $with = [
        'followable',
        'follower',
    ];

    /** @inheritdoc */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('lecturize.followers.table', 'followers');
    }

    /**
     * Morph followables.
     *
     * @return MorphTo
     */
    public function followable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Morph followers.
     *
     * @return MorphTo
     */
    public function follower(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Query by a followable item.
     *
     * @param  Builder  $query
     * @param  Model    $followable
     * @return mixed
     */
    public function scopeFollowing(Builder$query, Model $followable): Builder
    {
        return $query->where('followable_id',   $followable->id)
                     ->where('followable_type', get_class($followable));
    }

    /**
     * Query by a follower.
     *
     * @param  Builder  $query
     * @param  Model    $follower
     * @return mixed
     */
    public function scopeFollowedBy(Builder$query, Model $follower): Builder
    {
        return $query->where('follower_id',   $follower->id)
                     ->where('follower_type', get_class($follower));
    }
}