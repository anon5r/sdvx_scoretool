<?php

class TrackController extends BaseController {

	public static $diffs = array('novice', 'advanced', 'exhaust', 'infinite');
	public static $grades = array('d', 'c', 'b', 'a', 'aa', 'aaa');

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

	public function getJson($title)
	{
		$output = array();
		//$track = Track::where('title', '=', $title)->with('effects')->first();
		$track = Track::with(array('effects', 'effects.play_datas' => function($query)
                {
                        $query->where('user_id', '=', 2)->orderBy('created_at', 'desc');
                }))->where('title', '=', $title)->where('available','=','1')->first();


		$output['id'] = $track->id;
		$output['title'] = $track->title;
		$output['artist'] = $track->artist;

		//grade
		$arr = array();
		$dict = array('d', 'c', 'b', 'a', 'aa', 'aaa');
		$q = DB::select('select difficulty, v, count(v) as count from (select effects.difficulty, effect_id, user_id, MAX(grade+0) as v from play_datas inner join effects on effects.id = effect_id inner join tracks on tracks.id = effects.track_id where tracks.id = '.
		$output['id'].' and not grade = "" and not user_id in (2, 456, 471, 472) group by user_id, effect_id ) tmp group by effect_id, v');
		foreach($q as $i){
			if(!isset($arr[$i->difficulty])){
				$arr[$i->difficulty] = array();
			}
			$arr[$i->difficulty][$dict[$i->v-1]] = $i->count;
		}
		$stat_grade = $arr;

		//medal
		$arr = array();
		$dict = array('crash', 'comp', 'uc', 'per');
		$q = DB::select('select difficulty, v, count(v) as count from (select effects.difficulty, effect_id, user_id, MAX(medal+0) as v from play_datas inner join effects on effects.id = effect_id inner join tracks on tracks.id = effects.track_id where tracks.id = '.
		$output['id'].' and not medal = "" group by user_id, effect_id ) tmp group by effect_id, v');
		foreach($q as $i){
			if(!isset($arr[$i->difficulty])){
				$arr[$i->difficulty] = array();
			}
			$arr[$i->difficulty][$dict[$i->v-1]] = $i->count;
		}
		$stat_medal = $arr;

		foreach($track->effects as $e){
			$effect_arr = array();
			$effect_arr['level'] = $e->level;
			$effect_arr['effected_by'] = $e->effected_by;
			$effect_arr['illustrated_by'] = $e->illustrated_by;
			$effect_arr['stat'] = array();
			if(isset($stat_grade[$e->difficulty])){
				$effect_arr['stat']['grade'] = $stat_grade[$e->difficulty];
			}
			if(isset($stat_medal[$e->difficulty])){
				$effect_arr['stat']['medal'] = $stat_medal[$e->difficulty];
			}
			$pd = $e->play_datas->first();
			if($pd){
				$effect_arr['stat']['average'] = $pd->highscore;
			}
			
			$output[$e->difficulty] = $effect_arr;
			
		}
        	return Response::json($output);
	}
	public function getHtml($title, $option=null)
	{
        	return View::make('track', array('title'=>$title));
	}
	public function searchHtml()
	{
		if(!isset($_GET)){
			return;
		}
		$title = isset($_GET['title'])?$_GET['title']:'';
		$artist = isset($_GET['artist'])?$_GET['artist']:'';
		$effected_by = isset($_GET['effected_by'])?$_GET['effected_by']:'';
		$illustrated_by = isset($_GET['illustrated_by'])?$_GET['illustrated_by']:'';
	
		$tracks = Track::where('title', 'like', '%'.$title.'%')
			->where('artist', 'like', '%'.$artist.'%')
			->where('available', '=', '1')
			->orderBy('created_at', 'desc');
		if($effected_by || $illustrated_by){
			$effects = Effect::where('effected_by', 'like', '%'.$effected_by.'%')
				->where('illustrated_by', 'like', '%'.$illustrated_by.'%')
				->groupBy('track_id');
			$ids = array();
			foreach($effects->get() as $e){
				array_push($ids, $e->track_id);
			}
			if(count($ids) > 0){
				$tracks->whereIn('id', $ids);
			}else{
				return;
			}
		}
		$results = $tracks->get();
        	return View::make('track_search', array(
			'results'=>$results,
			'title'=>$title,
			'artist'=>$artist,
			'effected_by'=>$effected_by,
			'illustrated_by'=>$illustrated_by
		));
	}
}
