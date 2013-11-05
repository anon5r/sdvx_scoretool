@extends('template')
@section('content')
<h1>ユーザー登録</h1>
@if (Session::has('same_name_errors'))
	<span class="error">同じIDがすでに存在します！</span>
@endif
@if (Session::has('db_errors'))
	<span class="error">DBエラー（@quick_4xにご連絡ください）</span>
@endif
@if (Session::has('input_errors'))
	<span class="error">入力内容がおかしいです！</span>
@endif
{{Form::open(array('register'))}}
<p>{{Form::label('name', 'ID（英数字のみ16文字以下）')}}</p>
<p>{{Form::text('name')}}</p>
<p>{{Form::label('password', 'パスワード')}}</p>
<p>{{Form::password('password')}}</p>
<p>{{Form::submit('登録')}}</p>
{{Form::close()}}
@stop
