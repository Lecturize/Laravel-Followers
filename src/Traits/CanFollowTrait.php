<?php namespace vendocrat\Followers\Traits;

use vendocrat\Followers\Exceptions\AlreadyFollowingException;
use vendocrat\Followers\Exceptions\CannotBeFollowedException;
use vendocrat\Followers\Exceptions\FollowerNotFoundException;
use vendocrat\Followers\Models\Followable;

use Illuminate\Database\Eloquent\Model;

trait CanFollowTrait
{
	/**
	 * Get all followable items this model morphs to as a follower
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function followable()
	{
		return $this->morphMany('vendocrat\Followers\Models\Followable', 'followable');
	}

	/**
	 * @param $query
	 * @return mixed
	 */
	public function scopeFollows( $query )
	{
		$model = $this;
		return $query->whereHas('followable', function($q) use($model) {
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
			return Followable::following( $followable )
				->followedBy( $this )
				->delete();
		}

		throw new FollowerNotFoundException( get_class($this) .'::'. $this->id .' is not following '. get_class($followable) .'::'. $followable->id );
	}

	/**
	 * @param Model $followable
	 * @return bool
	 */
	public function isFollowing( Model $followable )
	{
		$query = Followable::following( $followable )
			->followedBy( $this );

		return $query->count() > 0;
	}

	/**
	 * @return mixed
	 */
	public function getFollowingCount( $type = '' )
	{
		$followables = Followable::
		      where('follower_id',   $this->id)
			->where('follower_type', get_class($this))
			//	->where( 'followable_type', 'like', '%'. $type .'%' );
			->get();

		return $followables->count();
	}

	/**
	 * @param string $type
	 * @return mixed
	 */
	public function getFollowing( $type = '' )
	{
		$followables = Followable::
			  where('follower_id',   $this->id)
			->where('follower_type', get_class($this))
		//	->where( 'followable_type', 'like', '%'. $type .'%' );
			->get();

		$return = array();

		foreach ( $followables as $followable )
		{
			$return[] = $followable->followable()->first();
		}

		return collect($return)->shuffle();
	}
}