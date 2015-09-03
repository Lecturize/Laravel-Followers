<?php namespace vendocrat\Followers\Traits;

use vendocrat\Followers\Exceptions\AlreadyFollowingException;
use vendocrat\Followers\Exceptions\CannotBeFollowedException;
use vendocrat\Followers\Exceptions\FollowerNotFoundException;
use vendocrat\Followers\Models\Followable;

use Illuminate\Database\Eloquent\Model;

trait CanFollowTrait
{
	/**
	 * Get all items this model is following
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function following()
	{
		return $this->morphMany('vendocrat\Followers\Models\Followable', 'follower');
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

		if ( $followers = $followable->followers() )
		{
			return $followers->create([
				'follower_id'   => $this->id,
				'follower_type' => get_class($this),
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
				->follower( $this )
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
			->follower( $this );

		return $query->count() > 0;
	}

	/**
	 * @return mixed
	 */
	public function getFollowing()
	{
		return $this->following()->get();
	}

	/**
	 * @return mixed
	 */
	public function getFollowingCount()
	{
		return $this->following->count();
	}
}