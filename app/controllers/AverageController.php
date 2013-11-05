<?php

class AverageController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function calc()
	{
		/*
			Blacklist
			2: average
			456: testman
			471: testman2
			472: testman3
		*/
		if(Auth::guest() || Auth::user()->name != 'sample'){
			App::abort(404);
			return;
		}
		$q = DB::select('select tracks.title as title, effects.difficulty as difficulty, avg(highscore) as average from (select effect_id, user_id, max(highscore) as highscore from play_datas where highscore >= 0 and not user_id in (2, 456, 471, 472) group by user_id, effect_id) tmp inner join effects on effects.id = tmp.effect_id inner join tracks on tracks.id = effects.track_id group by effect_id');
		$tracks = array();
		foreach($q as $i){
			$title = $i->title;
			$difficulty = $i->difficulty;
			$average = floor($i->average);
			echo $title.' '.$difficulty.' '.$average.'<br>';
			$found = false;
			foreach($tracks as &$t){
				if($t['title'] == $title){
					$t[$difficulty] = array('highscore' => $average, 'play_count'=>0);
					$found = true;
					break;
				}
			}
			if(!$found){
				$track = array();
				$track['title'] = $title;
				$track[$difficulty] = array('highscore' => $average, 'play_count'=>0);
				array_push($tracks, $track);
			}
		}
		$profile = array(
			'name' => 'AVERAGE',
			'play_count' => '0',
			'skill' => '-',
			'packet' => 0,
			'block' => 0,
			'tracks' => $tracks
		);
		$profile_json = json_encode($profile);
		$user = User::where('name', '=', 'average')->first();
		return UploadController::upload_profile_for_average($user, $profile_json);
	}

}
