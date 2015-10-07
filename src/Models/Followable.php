<?php namespace vendocrat\Followers\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Followable
 * @package vendocrat\Followers\Models
 */
class Followable extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'followables';

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
	protected $dates = ['deleted_at'];

	/**
	 * The relations to eager load on every query.
	 *
	 * @var array
	 */
	protected $with = ['followable', 'follower'];

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
	 * Morph followers
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function follower()
	{
		return $this->morphTo();
	}

	/**
	 * @param  object $query
	 * @param  Model  $followable
	 * @return mixed
	 */
	public function scopeFollowing( $query, Model $followable )
	{
		return $query
			->where( 'followable_id',   $followable->id )
			->where( 'followable_type', get_class($followable) );
	}

	/**
	 * @param  object $query
	 * @param  Model  $follower
	 * @return mixed
	 */
	public function scopeFollowedBy( $query, Model $follower )
	{
		return $query
			->where( 'follower_id',   $follower->id )
			->where( 'follower_type', get_class($follower) );
	}
}