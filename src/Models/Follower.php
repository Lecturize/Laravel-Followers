<?php namespace Lecturize\Followers\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Follower
 * @package Lecturize\Followers\Models
 */
class Follower extends Model
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'follower_id',
        'follower_type',
        'followable_id',
        'followable_type',
    ];

    /**
     * @inheritdoc
     */
    protected $dates = ['deleted_at'];

    /**
     * @inheritdoc
     */
    protected $with = ['followable', 'follower'];

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->table = config('lecturize.followers.table', 'followers');
    }

    /**
     * Morph followables
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function followable()
    {
        return $this->morphTo();
    }

    /**
     * Morph followers
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function follower()
    {
        return $this->morphTo();
    }

    /**
     * @param  object $query
     * @param  Model  $followable
     * @return mixed
     */
    public function scopeFollowing( $query, Model $followable )
    {
        return $query
            ->where( 'followable_id',   $followable->id )
            ->where( 'followable_type', get_class($followable) );
    }

    /**
     * @param  object $query
     * @param  Model  $follower
     * @return mixed
     */
    public function scopeFollowedBy( $query, Model $follower )
    {
        return $query
            ->where( 'follower_id',   $follower->id )
            ->where( 'follower_type', get_class($follower) );
    }
}