<?php namespace Lecturize\Followers\Traits;

use Lecturize\Followers\Exceptions\AlreadyFollowingException;
use Lecturize\Followers\Exceptions\CannotBeFollowedException;
use Lecturize\Followers\Exceptions\FollowerNotFoundException;
use Lecturize\Followers\Models\Follower;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CanFollow
 * @package Lecturize\Followers\Traits
 */
trait CanFollow
{
    /**
     * Get all followable items this model morphs to as a follower.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function followables()
    {
        return $this->morphMany(Follower::class, 'follower');
    }

    /**
     * Follow a followable model.
     *
     * @param  mixed  $followable
     * @return mixed
     *
     * @throws AlreadyFollowingException
     * @throws CannotBeFollowedException
     */
    public function follow($followable)
    {
        if ($isFollower = $this->isFollowing($followable) !== false) {
            throw new AlreadyFollowingException( get_class($this) .'::'. $this->id .' is already following '. get_class($followable) .'::'. $followable->id );
        }

        if ($followable->follower()) {
            cache()->forget($this->getFollowingCacheKey());

            return Follower::create([
                'follower_id'     => $this->id,
                'follower_type'   => get_class($this),
                'followable_id'   => $followable->id,
                'followable_type' => get_class($followable),
            ]);
        }

        throw new CannotBeFollowedException(get_class($followable) .'::'. $followable->id .' cannot be followed.');
    }

    /**
     * Unfollow a followable model.
     *
     * @param  mixed  $followable
     * @return mixed
     *
     * @throws FollowerNotFoundException
     */
    public function unfollow($followable)
    {
        if ($isFollower = $this->isFollowing($followable) === true) {
            cache()->forget($this->getFollowingCacheKey());

            return Follower::following($followable)
                           ->followedBy($this)
                           ->delete();
        }

        throw new FollowerNotFoundException(get_class($this) .'::'. $this->id .' is not following '. get_class($followable) .'::'. $followable->id);
    }

    /**
     * Check whether this model is following a given followable model.
     *
     * @param  mixed  $followable
     * @return bool
     */
    public function isFollowing($followable)
    {
        $query = Follower::following($followable)
                         ->followedBy($this);

        return (bool) $query->count() > 0;
    }

    /**
     * Get the following count.
     *
     * @return int
     */
    public function getFollowingCount()
    {
        $key = $this->getFollowingCacheKey();

        return cache()->remember($key, config('lecturize.followers.cache.expiry', 10), function() {
            $count = 0;
            Follower::where('follower_id',   $this->id)
                    ->where('follower_type', get_class($this))
                    ->chunk(1000, function ($models) use (&$count) {
                          $count = $count + count($models);
                      });

            return $count;
        });
    }

    /**
     * @param  int     $limit
     * @param  string  $type
     * @return mixed
     */
    public function getFollowing($limit = 0, $type = '')
    {
        if ($type) {
            $followables = $this->followables()->where('followable_type', $type)->get();
        } else {
            $followables = $this->followables()->get();
        }

        $return = [];
        foreach ($followables as $followable)
            $return[] = $followable->followable()->first();

        $collection = collect($return)->shuffle();

        if ($limit == 0)
            return $collection;

        return $collection->take($limit);
    }

    /**
     * Get the cache key.
     *
     * @return string
     */
    private function getFollowingCacheKey()
    {
        $model = get_class($this);
        $model = substr($model, strrpos($model, '\\') + 1);
        $model = strtolower($model);

        return 'followers.'. $model .'.'. $this->id .'.following.count';
    }

    /**
     * Scope follows.
     *
     * @param  object  $query
     * @return mixed
     */
    public function scopeFollows($query)
    {
        return $query->whereHas('followables', function($q) {
            $q->where('follower_id',   $this->id);
            $q->where('follower_type', get_class($this));
        });
    }
}