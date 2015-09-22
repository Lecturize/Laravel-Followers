<?php namespace vendocrat\Followers\Traits;

use vendocrat\Followers\Exceptions\AlreadyFollowingException;
use vendocrat\Followers\Exceptions\CannotBeFollowedException;
use vendocrat\Followers\Exceptions\FollowerNotFoundException;
use vendocrat\Followers\Models\Followable;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FollowableTrait
 * @package vendocrat\Followers\Traits
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
		return $this->morphMany('vendocrat\Followers\Models\Followable', 'follower');
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
		if ( $hasFollower = $this->hasFollower($follower) !== false ) {
			throw new AlreadyFollowingException( get_class($follower) .'::'. $follower->id .' is already following '. get_class($this) .'::'. $this->id );
		}

		if ( $follower->followable() )
		{
			return Followable::create([
				'follower_id'     => $follower->id,
				'follower_type'   => get_class($follower),
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
		if ( $hasFollower = $this->hasFollower($follower) === true )
		{
			return Followable::
				  followedBy( $follower )
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
		$query = Followable::
			  followedBy( $follower )
			->following( $this );

		return $query->count() > 0;
	}

	/**
	 * @return mixed
	 */
	public function getFollowerCount()
	{
		$followers = Followable::
			  where('followable_id',   $this->id)
			->where('followable_type', get_class($this))
			->get();

		return $followers->count();
	}

	/**
	 * @param string $type
	 * @return mixed
	 */
	public function getFollowers( $type = '' )
	{
		if ( $type ) {
			$followers = Followable::
				  where('followable_id',   $this->id)
				->where('followable_type', get_class($this))
				->where('follower_type', 'like', '%'. $type .'%')
				->get();
		} else {
			$followers = Followable::
				  where('followable_id',   $this->id)
				->where('followable_type', get_class($this))
				->get();
		}

		$return = array();

		foreach ( $followers as $follower )
		{
			$return[] = $follower->follower()->first();
		}

		return collect($return)->shuffle();
	}
}