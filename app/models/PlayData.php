<?php

class PlayData extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'play_datas';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('id', 'user_id', 'effect_id', 'created_at', 'updated_at');

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function effect()
	{
		return $this->belongsTo('Effect');
	}

	public static function findOrCreate($user, $effect, $json){
		$medal = isset($json->{'medal'}) ? $json->{'medal'} : '';
		$grade = isset($json->{'grade'}) ? $json->{'grade'} : '';
		$highscore = isset($json->{'highscore'}) ? $json->{'highscore'} : -1;
		$play_count = isset($json->{'play_count'}) ? $json->{'play_count'} : -1;
		$comp_count = isset($json->{'comp_count'}) ? $json->{'comp_count'} : -1;
		$uc_count = isset($json->{'uc_count'}) ? $json->{'uc_count'} : -1;
		$per_count = isset($json->{'per_count'}) ? $json->{'per_count'} : -1;
		$play_data = PlayData::
			where('user_id', '=', $user->id)->
			where('effect_id', '=', $effect->id)->
			where('medal', '=', $medal)->
			where('highscore', '=', $highscore)->
			where('play_count', '=', $play_count)->
			where('uc_count', '=', $uc_count)->
			where('per_count', '=', $per_count)->
			first();
		if(!$play_data){
			$play_data = new PlayData();
			$play_data->medal = $medal;
			$play_data->grade = $grade;
			$play_data->highscore = $highscore;
			$play_data->play_count = $play_count;
			$play_data->comp_count = $comp_count;
			$play_data->uc_count = $uc_count;
			$play_data->per_count = $per_count;
			$effect->play_datas()->save($play_data);
			$user->play_datas()->save($play_data);
		}
		return $play_data;
	}
}
