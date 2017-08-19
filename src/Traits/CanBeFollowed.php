<?php namespace Lecturize\Followers\Traits;

use Lecturize\Followers\Exceptions\AlreadyFollowingException;
use Lecturize\Followers\Exceptions\CannotBeFollowedException;
use Lecturize\Followers\Exceptions\FollowerNotFoundException;
use Lecturize\Followers\Models\Follower;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CanBeFollowed
 * @package Lecturize\Followers\Traits
 */
trait CanBeFollowed
{
	/**
	 * Get all followable items this model morphs to as being followed
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function follower()
	{
		return $this->morphMany(Follower::class, 'followable');
	}

	/**
	 * Add a follower.
	 *
	 * @param  mixed  $follower
	 * @return mixed
     *
	 * @throws AlreadyFollowingException
	 * @throws CannotBeFollowedException
	 */
	public function addFollower($follower)
	{
		// check if $follower is already following this
		if ($hasFollower = $this->hasFollower($follower) !== false)
			throw new AlreadyFollowingException(get_class($follower) .'::'. $follower->id .' is already following '. get_class($this) .'::'. $this->id);

		// check if $follower can follow (has CanFollow)
		if (! $follower->followables())
			throw new CannotBeFollowedException(get_class($follower) .'::'. $follower->id .' cannot follow this.');

        cache()->forget($this->getFollowerCacheKey());

		return Follower::create([
			'follower_id'     => $follower->id,
			'follower_type'   => get_class($follower),
			'followable_id'   => $this->id,
			'followable_type' => get_class($this),
		]);
	}

	/**
	 * Delete a follower.
	 *
	 * @param  mixed  $follower
	 * @return mixed
	 * @throws FollowerNotFoundException
	 */
	public function deleteFollower($follower)
	{
		if ($hasFollower = $this->hasFollower($follower) === true) {
            cache()->forget($this->getFollowerCacheKey());

			return Follower::followedBy($follower)
						   ->following($this)
                           ->delete();
		}

		throw new FollowerNotFoundException(get_class($follower) .'::'. $follower->id .' is not following '. get_class($this) .'::'. $this->id);
	}

	/**
     * Check whether this model has a given follower.
     *
	 * @param  mixed  $follower
	 * @return bool
	 */
	public function hasFollower($follower)
	{
		$query = Follower::followedBy($follower)
						 ->following($this);

		return (bool) $query->count() > 0;
	}

	/**
	 * Get the follower count.
     *
	 * @return int
	 */
	public function getFollowerCount()
	{
		$key = $this->getFollowerCacheKey();

        return cache()->remember($key, config('lecturize.followers.cache.expiry', 10), function() {
            $count = 0;
            Follower::where('followable_id',   $this->id)
                    ->where('followable_type', get_class($this))
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
	public function getFollowers($limit = 0, $type = '')
	{
		if ($type) {
			$followers = $this->follower()->where('follower_type', $type)->get();
		} else {
			$followers = $this->follower()->get();
		}

		$return = [];
		foreach ($followers as $follower)
			$return[] = $follower->follower()->first();

		$collection = collect($return)->shuffle();

		if ($limit === 0)
			return $collection;

		return $collection->take($limit);
	}

	/**
     * Get the cache key.
     *
	 * @return string
	 */
	private function getFollowerCacheKey()
	{
        $model = get_class($this);
        $model = substr($model, strrpos($model, '\\') + 1);
        $model = strtolower($model);

		return 'followers.'. $model .'.'. $this->id .'.follower.count';
	}

    /**
     * Scope followers.
     *
     * @param  object  $query
     * @return mixed
     */
    public function scopeFollowers($query)
    {
        return $query->whereHas('follower', function($q) {
            $q->where('followable_id',   $this->id);
            $q->where('followable_type', get_class($this));
        });
    }
}