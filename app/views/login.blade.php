@extends('template')
@section('content')
<h1>ログイン</h1>
@if (Session::has('auth_errors'))
	<span class="error">IDまたはパスワードが違います</span>
@endif
@if (Session::has('db_errors'))
	<span class="error">DBエラー（@quick_4xにご連絡ください）</span>
@endif
{{Form::open(array('register'))}}
<p>{{Form::label('name', 'ID')}}</p>
<p>{{Form::text('name')}}</p>
<p>{{Form::label('password', 'パスワード')}}</p>
<p>{{Form::password('password')}}</p>
<p>{{Form::submit('ログイン')}}</p>
{{Form::close()}}
@stop
