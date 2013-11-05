<?php

class Effect extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'effects';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('id', 'track_id', 'created_at', 'updated_at');

	public function track()
	{
		return $this->belongsTo('Track');
	}

	public function play_datas()
	{
		return $this->hasMany('PlayData');
	}

	public static function findOrCreate($track, $diff, $json)
	{
		$effect = $track->effects()->where('difficulty', '=', $diff)->first();
		if(!$effect){
			$effect = new Effect();
			$effect->difficulty = $diff;
			$effect->level = $json->{'level'};
			$effect->effected_by = $json->{'effected_by'};
			$effect->illustrated_by = $json->{'illustrated_by'};
			$effect->available = true;
			$track->effects()->save($effect);
		}
		return $effect;
	}
	public static function findOrCreate2($track, $diff, $json)
	{
		$effect = null;
		if(isset($track->effects)){
			foreach($track->effects as $e){
				if($e->difficulty == $diff){
					$effect = $e;
					break;
				}
			}
		}
		if(!$effect){
			$effect = new Effect();
			$effect->difficulty = $diff;
			$effect->level = $json->{'level'};
			$effect->effected_by = $json->{'effected_by'};
			$effect->illustrated_by = $json->{'illustrated_by'};
			$effect->available = true;
			$track->effects()->save($effect);
		}
		return $effect;
	}
}
