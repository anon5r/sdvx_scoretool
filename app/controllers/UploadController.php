<?php

class UploadController extends BaseController {

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
	/* no longer used
	public function upload()
	{
		$input = Input::all();
		if(Auth::guest()){
			Redirect::to('/login');
		}
		$user = Auth::user();
		if(isset($input['show_play_count'])){
			$user->show_play_count = intval($input['show_play_count']) != 0;
			$user->save();
			return View::make('redirect', array('url'=>'/user/'.$user->name, 'message'=>'公開設定を変更しました'));
		}
		if(isset($input['profile'])){
			$input_profile = $input['profile'];
			$hashed_profile = md5($input_profile);
			$p_json = json_decode($input_profile);
			//$pre_profile = $user->profiles()->orderBy('created_at', 'desc')->first();
			if($hashed_profile != $user->hashed_last_profile){
				//DB::transaction(function(){
				$profile = Profile::findOrCreate($p_json);
				$ts_json = array_reverse($p_json->{'tracks'});
				foreach($ts_json as $t_json){
					$track = Track::findOrCreate($t_json);
					if(!$track){
						continue;
					}
					foreach(UploadController::$diffs as $diff){
						if(!isset($t_json->{$diff})){
							continue;
						}
						$e_json = $t_json->{$diff};
						$effect = Effect::findOrCreate($track, $diff, $e_json);
						$play_data = PlayData::findOrCreate($user, $effect, $e_json);
					}
				}
				$user->profiles()->save($profile);
				$user->hashed_last_profile = $hashed_profile;
				$user->save();
				//});
				return View::make('redirect', array('url'=>'/user/'.$user->name, 'message'=>'アップロードが完了しました'));
			}else{
				return View::make('redirect', array('url'=>'/user/'.$user->name, 'message'=>'更新がありませんでした'));
			}
		}
		//return Redirect::to('/user/'.$user->name)=>with(array('message'=>'アップロードが完了しました'));
	}
	*/
	public function upload2()
        {
                $input = Input::all();
                if(Auth::guest()){
                        Redirect::to('/login');
                }
                $user = Auth::user();
                if(isset($input['show_play_count'])){
                        $user->show_play_count = intval($input['show_play_count']) != 0;
                        $user->save();
                        return View::make('redirect', array('url'=>'/user/'.$user->name, 'message'=>'公開設定を変更しました'));
                }
                if(isset($input['profile'])){
                        $input_profile = $input['profile'];
                        $hashed_profile = md5($input_profile);
                        $p_json = json_decode($input_profile);
                        if($hashed_profile != $user->hashed_last_profile){
				//echo $input_profile;
				
				DB::transaction(function() use($user, $p_json, $hashed_profile)
 				{
                                $profile = Profile::findOrCreate($p_json);
                                $ts_json = array_reverse($p_json->{'tracks'});
                                foreach($ts_json as $t_json){
                                        $track = Track::findOrCreate2($t_json);
                                        if(!$track){
                                                continue;
                                        }
                                        foreach(UploadController::$diffs as $diff){
                                                if(!isset($t_json->{$diff})){
                                                        continue;
                                                }
                                                $e_json = $t_json->{$diff};
                                                $effect = Effect::findOrCreate2($track, $diff, $e_json);
					
                                                $play_data = PlayData::findOrCreate($user, $effect, $e_json);
                                        }
                                }
                                $user->profiles()->save($profile);
                                $user->hashed_last_profile = $hashed_profile;
                                $user->save();
				});
                                return View::make('redirect', array('url'=>'/user/'.$user->name, 'message'=>'アップロードが完了しました'));
                        }else{
                                return View::make('redirect', array('url'=>'/user/'.$user->name, 'message'=>'更新がありませんでした'));
                        }
                }
        }
	public static function upload_profile_for_average($user, $input_profile)
	{
		$hashed_profile = md5($input_profile);
		if($user->hashed_last_profile != $hashed_profile){
			$p_json = json_decode($input_profile);
			$profile = Profile::findOrCreate($p_json);
			$ts_json = array_reverse($p_json->{'tracks'});
			foreach($ts_json as $t_json){
				$track = Track::findOrCreate($t_json);
				foreach(UploadController::$diffs as $diff){
					if(!isset($t_json->{$diff})){
						continue;
					}
					$e_json = $t_json->{$diff};
					$effect = Effect::findOrCreate($track, $diff, $e_json);
					$play_data = PlayData::findOrCreate($user, $effect, $e_json);
					echo $play_data.'<br>';
				}
			}
			$user->profiles()->save($profile);
			$user->hashed_last_profile = $hashed_profile;
			$user->save();
		}
	}
}
