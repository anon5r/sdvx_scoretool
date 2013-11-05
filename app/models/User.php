<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('id', 'name', 'hashed_password', 'hashed_last_profile', 'available', 'created_at', 'updated_at');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->hashed_password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function profiles()
	{
		return $this->hasMany('Profile');
	}

	public function play_datas()
	{
		return $this->hasMany('PlayData');
	}

	public static function myCreate($name, $password)
	{
		$user = new User();
		$user->name = $name;
		$user->hashed_password = Hash::make($password);
		//$user->privates = '{"play_count":1}';
		$user->available = true;
		return $user;
	}
}
