<?php

class ProfileController extends BaseController {

	public static $diffs = array('novice', 'advanced', 'exhaust', 'infinite');

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

	public function getJson($name, $n=null, $y=null, $m=null, $d=null)
	{
		$grades = array('', 'd', 'c', 'b', 'a', 'aa', 'aaa');
		$updated = isset($_GET['updated'])?$_GET['updated']:0;
		$played = isset($_GET['played'])?$_GET['played']:0;
		$n = isset($_GET['page'])?$_GET['page']:$n;
		$user = User::where('name', '=', $name)->first();
        	if(!$user){
                	App::abort(404);
                	return;
        	}
	        $show_play_count = intval($user->show_play_count) != 0;

		$profile = null;
		if($n == null){
			$n = -1;
		}else{
			$n = intval($n);
		}

		if($n >= 0){
			$m = $n+1;
			$pm = $n;
			$profiles = $user->profiles()->orderBy('created_at', 'asc')->take($m+1)->get();
		}else{
			$m = -$n;
			$pm = -($n-1);
			$profiles = $user->profiles()->orderBy('created_at', 'desc')->take($m+1)->get();
		}
		if(count($profiles) >= $m){
	       		$profile = $profiles[$m-1];
			if($updated){
				if(!isset($profiles[$pm-1])){
					$pre_profile = null;
				}else{
	       				$pre_profile = $profiles[$pm-1];
				}
			}
		}
	        if(!$profile){
	                App::abort(404);
	                return;
	        }
		$date_str = $profile->created_at->format('Y-m-d H:i:s');
		if($updated && $pre_profile){
			$pre_date_str = $pre_profile->created_at->format('Y-m-d H:i:s');
		}else{
			$pre_date_str = null; 
		}

	        $profile_arr = array();
	        $profile_arr['name'] = $profile->name;
	        if($show_play_count){
	                $profile_arr['packet'] = $profile->packet;
	                $profile_arr['play_count'] = $profile->play_count;
	                $profile_arr['block'] = $profile->block;
	        }
        	$profile_arr['skill'] = $profile->skill;

        	$tracks_arr = array();
        	foreach(Track::with(array('effects', 'effects.play_datas' => function($query)use($user, $date_str, $pre_date_str)
        	{
        	        $query->where('user_id', '=', $user->id)->where('created_at', '<=', $date_str)->orderBy('created_at', 'desc');
			if($pre_date_str){
				$query->where('created_at', '>', $pre_date_str);
			}
        	}))->where('available','=','1')->get() as $track){
                	$track_arr = array();
                	$track_arr['title'] = $track->title;
                	$track_arr['id'] = $track->id;
			$has_play_data = false;
                	foreach($track->effects as $effect){
                	        $effect_arr = array();
                	        $effect_arr['level'] = intval($effect->level);
				$play_data = $effect->play_datas->first();
                	        if($play_data && $play_data->play_count >= 0){
					$has_play_data = true;
					/*
                	                if($play_data->grade != ''){
                	                       	$effect_arr['grade'] = $play_data->grade;
					}
					*/
                	                if($play_data->grade != ''){
                	                       	$effect_arr['grade'] = $play_data->grade;
					}else if($play_data->play_count > 0){
						//select the best grade
						$q = DB::select('select max(grade+0) as grade_num from play_datas where effect_id = '.$effect->id.' and user_id = '.$user->id);
						$val = isset($q[0]->{'grade_num'})?$q[0]->{'grade_num'}:0;
						if($val){
							$effect_arr['grade'] = $grades[$val];
						}
					}
                	                if($play_data->medal != ''){
                	                       	$effect_arr['medal'] = $play_data->medal;
                	                }
					if($play_data->per_count > 0){
                	                       	$effect_arr['medal'] = 'per';
						$effect_arr['grade'] = 'aaa';
                	                }else if($play_data->uc_count > 0){
                	                       	$effect_arr['medal'] = 'uc';
                	                }else if($play_data->comp_count > 0){
                	                       	$effect_arr['medal'] = 'comp';
                	                }else if($play_data->play_count > 0){
                	                       	$effect_arr['medal'] = 'crash';
					}
                	                if(intval($play_data->highscore) >= 0){
                	                       	$effect_arr['highscore'] = intval($play_data->highscore);
                	                }
                	                if($show_play_count){
                	                       	$effect_arr['play_count'] = intval($play_data->play_count);
                	                       	$effect_arr['comp_count'] = intval($play_data->comp_count);
                	                       	$effect_arr['uc_count'] = intval($play_data->uc_count);
                	                       	$effect_arr['per_count'] = intval($play_data->per_count);
                	                }
                	        	$track_arr[$effect->difficulty] = $effect_arr;
                	        }else if(!$played){
                	        	$track_arr[$effect->difficulty] = $effect_arr;
				}
                	}
			if($has_play_data || !$played){
                		array_push($tracks_arr, $track_arr);
			}
        	}
        	$profile_arr["tracks"] = $tracks_arr;
        	$output = array();
        	$output["profile"] = $profile_arr;
        	$output["show_play_count"] = $show_play_count;
		$output['created_at'] = $date_str;
		$output['name'] = $name;
		$output['n'] = $n;
        	return Response::json($output);
	}
	public function getHtml($name, $option=null)
	{
        	$is_me = false;
        	$show_play_count = false;
        	if(Auth::guest()){
			$user = null;
        	}else{
                	$user = Auth::user();
                	if($user->name == $name){
                        	$is_me = true;
                        	$show_play_count = intval($user->show_play_count) != 0;
                	}
        	}
        	return View::make('user', array(
			'option'=>$option, 
			'name'=>$name, 
			'is_me'=>$is_me, 
			'show_play_count'=>$show_play_count,
			'user'=>$user
		));
	}
}
