<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('index');
});

Route::get('/register', function()
{
	return View::make('register');
});

Route::post('/register', function()
{
	$input = Input::all();
	$rules = array(
		'name' => 'required|min:1|max:16|alpha_dash',
		'password' => 'required'
	);
	$v = Validator::make($input, $rules);
	if($v->fails()){
		return Redirect::to('/register')
			->with('input_errors', true);
	}
	$name = $input['name'];
	$password = $input['password'];
	try{
		if(User::where('name', '=', $name)->count() > 0){
			return Redirect::to('/register')
				->with('same_name_errors', true);
		}
		$user = User::myCreate($name, $password);
		$user->save();
		if (Auth::attempt(array('name' => $name, 'password' => $password)))
		{
			return Redirect::to('/scoreviewer')
				->with('message', 'IDを作成しました！');
		}else{
			return Redirect::to('/login')
				->with('auth_errors', true);
		}
	}catch(Exception $e){
		return Redirect::to('/register')
			->with('db_errors', true);
	}
});

Route::get('/login', function()
{
	if (Auth::guest()){
		return View::make('login');
	}else{
		return Redirect::to('scoreviewer');
	}
});
Route::post('/login', function()
{
	$input = Input::all();
	$name = $input['name'];
	$password = $input['password'];
	try{
		if (Auth::attempt(array('name' => $name, 'password' => $password)))
		{
			return Redirect::to('/scoreviewer')
				->with('message', 'ログインしました');
		}else{
			return Redirect::to('/login')
				->with('auth_errors', true);
		}
	}catch(Exception $e){
		return Redirect::to('/login')
			->with('db_errors', true);
	}
});
Route::get('/logout', function()
{
	Auth::logout();
	return Redirect::to('/')
		->with('message', 'ログアウトしました');
});
Route::post('/upload', 'UploadController@upload2');
/*
Route::post('/upload2', 'UploadController@upload2');
Route::get('/scoretool_test', function()
{
	$no_header = true;
	return View::make('scoretool_test', array('no_header'=>true));
});
*/
Route::get('/scoretool', function()
{
	$no_header = true;
	return View::make('scoretool', array('no_header'=>true));
});
Route::get('/scoreviewer', function()
{
	return View::make('scoreviewer');
});
/*
Route::get('/scoreviewer_test', function()
{
	return View::make('scoreviewer_test');
});
*/
Route::get('/title2id.json', function()
{
	$callback = Input::get('callback');
	$result = array();
	foreach(Title::all() as $title){
		$result[$title->title] = intval($title->track_id);
	}
	Response::json($result, 200, array(
		'Content-Type' => 'application/javascript'
	));
	return $callback . '(' . json_encode($result) . ')';
});
Route::get('/user/{name}.json', 'ProfileController@getJson');
Route::get('/user/{name}/{n}.json', 'ProfileController@getJson');
Route::get('/user/{name}/{option}', 'ProfileController@getHtml');
Route::get('/user/{name}', 'ProfileController@getHtml');

Route::get('/track/{title}.json', 'TrackController@getJson')->where(array('title'=>'.+'));
Route::get('/track/{title}', 'TrackController@getHtml');
Route::get('/track', 'TrackController@searchHtml');
/*
Route::get('/user/{name}/{option}/vs/{name2}/{option2}', 'VsController@getHtml');
Route::get('/user/{name}/{option}/vs/{name2}', 'VsController@getHtml');
Route::get('/user/{name}/vs/{name2}/{option2}', 'VsController@getHtml');
Route::get('/user/{name}/vs/{name2}', 'VsController@getHtml');
*/
Route::get('/admin/calc_average', 'AverageController@calc');

