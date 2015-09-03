<?php namespace vendocrat\Followers\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'followers';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'follower_id',
		'follower_type',
		'followable_id',
		'followable_type',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = [];

	/**
	 * Morph followables
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function followable()
	{
		return $this->morphTo();
	}

	/**
	 * @param $query
	 * @param $business_id
	 * @return mixed
	 */
	public function scopeFollowing( $query, $business_id )
	{
		return $query->where( 'business_id', '=', $business_id );
	}

	/**
	 * @param $query
	 * @param $user_id
	 * @return mixed
	 */
	public function scopeFollower( $query, $user_id )
	{
		return $query->where( 'user_id', '=', $user_id );
	}
}