<?php

class Profile extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'profiles';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('id', 'user_id', 'created_at', 'updated_at');

	public function user()
	{
		return $this->belongsTo('User');
	}

	public static function findOrCreate($json)
	{
		$profile = new Profile();
		$profile->name = $json->{'name'};
		$profile->play_count = $json->{'play_count'};
		$profile->skill = isset($json->{'skill'})?$json->{'skill'}:'';
		$profile->packet = $json->{'packet'};
		$profile->block = $json->{'block'};
		return $profile;
	}
}
