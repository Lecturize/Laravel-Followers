<?php namespace vendocrat\Followers\Traits;

use vendocrat\Followers\Exceptions\AlreadyFollowingException;
use vendocrat\Followers\Exceptions\CannotBeFollowedException;
use vendocrat\Followers\Exceptions\FollowerNotFoundException;
use vendocrat\Followers\Models\Followable;

use Illuminate\Database\Eloquent\Model;

trait FollowableTrait
{
	/**
	 * Get all followers for this model
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function followers()
	{
		return $this->morphMany('vendocrat\Followers\Models\Followable', 'followable');
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
		if ( $isFollower = $this->isFollower($follower) !== false )
		{
			throw new AlreadyFollowingException( get_class($follower) .'::'. $follower->id .' is already following '. get_class($this) .'::'. $this->id );
		}

		if ( $followers = $follower->following() )
		{
			return $followers->create([
				'followable_id'   => $this->id,
				'followable_type' => get_class($this),
			]);
		}

		throw new CannotBeFollowedException( get_class($follower) .'::'. $follower->id .' cannot follow this.' );
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
		if ( $isFollower = $this->isFollower($follower) === true )
		{
			return Followable::follower( $follower )
				->following( $this )
				->delete();
		}

		throw new FollowerNotFoundException( get_class($follower) .'::'. $follower->id .' is not following '. get_class($this) .'::'. $this->id );
	}

	/**
	 * @param Model $follower
	 * @return bool
	 */
	public function isFollower( Model $follower )
	{
		$query = Followable::follower( $follower )
			->following( $this );

		return $query->count() > 0;
	}

	/**
	 * @return mixed
	 */
	public function getFollowers()
	{
		return $this->followers()->get();
	}

	/**
	 * @return mixed
	 */
	public function getFollowersCount()
	{
		return $this->followers->count();
	}
}