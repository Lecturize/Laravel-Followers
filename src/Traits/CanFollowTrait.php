<?php namespace vendocrat\Followers\Traits;

trait CanFollowTrait
{
	/**
	 * Get all items this model is following
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function following()
	{
		return $this->morphMany('vendocrat\Followers\Models\Follower', 'follower');
	}
}