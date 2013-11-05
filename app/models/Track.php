<?php

class Track extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tracks';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('id');

	public function titles()
	{
		return $this->hasMany('Title');
	}

	public function effects()
	{
		return $this->hasMany('Effect');
	}

	public static function findOrCreate($json)
	{
		if(!isset($json->{'title'})){
			return null;
		}
		$title_str = $json->{'title'};
		$title = Title::where('title', '=', $title_str)->first();
		if(!$title){
			$artist_str = isset($json->{'artist'})?$json->{'artist'}:'';
			$track = new Track();
			$track->title = $title_str;
			$track->artist = $artist_str;
			$track->available = true;
			$track->save();
			$title = new Title();
			$title->title = $title_str;
			$track->titles()->save($title);
		}else{
			$track = $title->track()->first();
		}
		return $track;
	}
	public static function findOrCreate2($json)
	{
		if(!isset($json->{'title'})){
			return null;
		}
		$title_str = $json->{'title'};
		$title = Title::with(array('track','track.effects'))->where('title', '=', $title_str)->first();
		if(!$title){
			$artist_str = isset($json->{'artist'})?$json->{'artist'}:'';
			$track = new Track();
			$track->title = $title_str;
			$track->artist = $artist_str;
			$track->available = true;
			$track->save();
			$title = new Title();
			$title->title = $title_str;
			$track->titles()->save($title);
		}else{
			$track = $title->track;
		}
		return $track;
	}
}
