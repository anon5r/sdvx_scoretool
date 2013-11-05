<?php

class VsController extends BaseController {

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
		$output = array();
        	return Response::json($output);
	}
	public function getHtml($name, $name2, $option=null, $option2=null)
	{
        	$is_me = false;
        	$show_play_count = false;
        	if(Auth::guest()){
        	}else{
                	$user_me = Auth::user();
        	}
        	return View::make('vs', array(
			'name'=>$name, 
			'name2'=>$name2,
			'option'=>isset($option)?$option:null, 
			'option2'=>isset($option2)?$option2:null, 
			'user_me'=>isset($user_me)?$user_me:null
		));
	}
}
