<?php namespace vendocrat\Followers\Traits;

trait FollowableTrait
{
	/**
	 * Get all followers for this model
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function followers()
	{
		return $this->morphMany('vendocrat\Followers\Models\Follower', 'followable');
	}
}