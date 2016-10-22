<?php namespace Lecturize\Followers\Traits;

use Lecturize\Followers\Exceptions\AlreadyFollowingException;
use Lecturize\Followers\Exceptions\CannotBeFollowedException;
use Lecturize\Followers\Exceptions\FollowerNotFoundException;
use Lecturize\Followers\Models\Followable;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CanFollowTrait
 * @package Lecturize\Followers\Traits
 */
trait CanFollowTrait
{
	/**
	 * Get all followable items this model morphs to as a follower
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function followables()
	{
		return $this->morphMany(Followable::class, 'follower');
	}

	/**
	 * @param $query
	 * @return mixed
	 */
	public function scopeFollows( $query )
	{
		$model = $this;
		return $query->whereHas('followables', function($q) use($model) {
			$q->where('follower_id',   $model->id);
			$q->where('follower_type', get_class($model));
		});
	}

	/**
	 * Follow method
	 *
	 * @param Model $followable
	 * @return mixed
	 * @throws AlreadyFollowingException
	 * @throws CannotBeFollowedException
	 */
	public function follow( Model $followable )
	{
		if ( $isFollower = $this->isFollowing($followable) !== false )
		{
			throw new AlreadyFollowingException( get_class($this) .'::'. $this->id .' is already following '. get_class($followable) .'::'. $followable->id );
		}

		if ( $followable->follower() )
		{
			$key = $this->getFollowingCacheKey();

			if ( config('lecturize.followers.cache.enable', true) )
				\Cache::forget($key);

			return Followable::create([
				'follower_id'     => $this->id,
				'follower_type'   => get_class($this),
				'followable_id'   => $followable->id,
				'followable_type' => get_class($followable),
			]);
		}

		throw new CannotBeFollowedException( get_class($followable) .'::'. $followable->id .' cannot be followed.' );
	}

	/**
	 * Unfollow method
	 *
	 * @param Model $followable
	 * @return mixed
	 * @throws FollowerNotFoundException
	 */
	public function unfollow( Model $followable )
	{
		if ( $isFollower = $this->isFollowing($followable) === true )
		{
			$key = $this->getFollowingCacheKey();

			if ( config('lecturize.followers.cache.enable', true) )
				\Cache::forget($key);

			return Followable::following( $followable )
							 ->followedBy( $this )
							 ->delete();
		}

		throw new FollowerNotFoundException( get_class($this) .'::'. $this->id .' is not following '. get_class($followable) .'::'. $followable->id );
	}

	/**
	 * @param $followable
	 * @return bool
	 */
	public function isFollowing( $followable )
	{
		$query = Followable::following( $followable )
						   ->followedBy( $this );

		return $query->count() > 0;
	}

	/**
	 * @param  bool $get_cached
	 * @return mixed
	 */
	public function getFollowingCount( $get_cached = true )
	{
		$key = $this->getFollowingCacheKey();

		if ( $get_cached && config('lecturize.followers.cache.enable', true) && \Cache::has($key) )
			return \Cache::get($key);

		$count = 0;
		Followable::where('follower_id',   $this->id)
				  ->where('follower_type', get_class($this))
				  ->chunk(1000, function ($models) use (&$count) {
					  $count = $count + count($models);
				  });

		if ( config('lecturize.followers.cache.enable', true) )
			\Cache::put($key, $count, config('lecturize.followers.cache.expiry', 10));

		return $count;
	}

	/**
	 * @param  int    $limit
	 * @param  string $type
	 * @return mixed
	 */
	public function getFollowing( $limit = 0, $type = '' )
	{
		if ( $type ) {
			$followables = $this->followables()->where('followable_type', $type)->get();
		} else {
			$followables = $this->followables()->get();
		}

		$return = [];
		foreach ( $followables as $followable )
		{
			$return[] = $followable->followable()->first();
		}

		$collection = collect($return)->shuffle();

		if ( $limit == 0 )
			return $collection;

		return $collection->take($limit);
	}

	/**
	 * @return string
	 */
	private function getFollowingCacheKey()
	{
		$id    = $this->id;
		$class = get_class($this);
		$type  = explode('\\', $class);

		$key = 'followers.'. end($type) .'.'. $id .'.following.count';
		$key = md5(strtolower($key));

		return $key;
	}
}