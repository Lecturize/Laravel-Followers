<?php namespace Lecturize\Followers\Traits;

use Lecturize\Followers\Exceptions\AlreadyFollowingException;
use Lecturize\Followers\Exceptions\CannotBeFollowedException;
use Lecturize\Followers\Exceptions\FollowerNotFoundException;
use Lecturize\Followers\Models\Followable;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FollowableTrait
 * @package Lecturize\Followers\Traits
 */
trait FollowableTrait
{
	/**
	 * Get all followable items this model morphs to as being followed
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function follower()
	{
		return $this->morphMany(Followable::class, 'followable');
	}

	/**
	 * @param $query
	 * @return mixed
	 */
	public function scopeFollowers( $query )
	{
		$model = $this;
		return $query->whereHas('follower', function($q) use($model) {
			$q->where('followable_id', $model->id);
			$q->where('followable_type', get_class($model));
		});
	}

	/**
	 * Add follower
	 *
	 * @param Model $follower
	 * @return mixed
	 * @throws AlreadyFollowingException
	 * @throws CannotBeFollowedException
	 */
	public function addFollower( Model $follower )
	{
		// check if $follower is already following this
		if ( $hasFollower = $this->hasFollower($follower) !== false )
			throw new AlreadyFollowingException( get_class($follower) .'::'. $follower->id .' is already following '. get_class($this) .'::'. $this->id );

		// check if $follower can follow (has CanFollowTrait)
		if ( ! $follower->followables() )
			throw new CannotBeFollowedException( get_class($follower) .'::'. $follower->id .' cannot follow this.' );

		$key = $this->getFollowerCacheKey();

		if ( config('lecturize.followers.cache.enable', true) )
			\Cache::forget($key);

		return Followable::create([
			'follower_id'     => $follower->id,
			'follower_type'   => get_class($follower),
			'followable_id'   => $this->id,
			'followable_type' => get_class($this),
		]);
	}

	/**
	 * Delete follower
	 *
	 * @param Model $follower
	 * @return mixed
	 * @throws FollowerNotFoundException
	 */
	public function deleteFollower( Model $follower )
	{
		if ( $hasFollower = $this->hasFollower($follower) === true )
		{
			$key = $this->getFollowerCacheKey();

			if ( config('lecturize.followers.cache.enable', true) )
				\Cache::forget($key);

			return Followable::followedBy( $follower )
							 ->following( $this )
							 ->delete();
		}

		throw new FollowerNotFoundException( get_class($follower) .'::'. $follower->id .' is not following '. get_class($this) .'::'. $this->id );
	}

	/**
	 * @param $follower
	 * @return bool
	 */
	public function hasFollower( $follower )
	{
		$query = Followable::followedBy( $follower )
						   ->following( $this );

		return $query->count() > 0;
	}

	/**
	 * @param  bool $get_cached
	 * @return mixed
	 */
	public function getFollowerCount( $get_cached = true )
	{
		$key = $this->getFollowerCacheKey();

		if ( $get_cached && config('lecturize.followers.cache.enable', true) && \Cache::has($key) )
			return \Cache::get($key);

		$count = 0;
		Followable::where('followable_id',   $this->id)
				  ->where('followable_type', get_class($this))
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
	public function getFollowers( $limit = 0, $type = '' )
	{
		if ( $type ) {
			$followers = $this->follower()->where('follower_type', $type)->get();
		} else {
			$followers = $this->follower()->get();
		}

		$return = [];
		foreach ( $followers as $follower )
		{
			$return[] = $follower->follower()->first();
		}

		$collection = collect($return)->shuffle();

		if ( $limit == 0 )
			return $collection;

		return $collection->take($limit);
	}

	/**
	 * @return string
	 */
	private function getFollowerCacheKey()
	{
		$id    = $this->id;
		$class = get_class($this);
		$type  = explode('\\', $class);

		$key = 'followers.'. end($type) .'.'. $id .'.follower.count';
		$key = md5(strtolower($key));

		return $key;
	}
}