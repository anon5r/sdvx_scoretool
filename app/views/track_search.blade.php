@extends('template')
@section('title')
	トラックデータ検索 - SDVX Score Tool ver.ii
@stop
@section('includes')
	@parent
	<link rel="stylesheet" href="/css/scoreviewer.css">
@stop
@section('tweet_button')
@stop
@section('menu')
	@parent
@stop
@section('content')
	<h1>トラック検索</h1>
	{{Form::open(array('url'=>'track','method'=>'GET'))}}
	<p>{{Form::label('title', 'タイトル')}}
	{{Form::text('title', $title)}}</p>
	<p>{{Form::label('artist', 'アーティスト')}}
	{{Form::text('artist', $artist)}}</p>
	<p>{{Form::label('effected_by', '譜面製作者')}}
	{{Form::text('effected_by', $effected_by)}}</p>
	<p>{{Form::label('illustrated_by', 'ジャケットアーティスト')}}
	{{Form::text('illustrated_by', $illustrated_by)}}</p>
	{{Form::submit('検索')}}</p>
	{{Form::close()}}
	@foreach ($results as $t)
		<div>
		<a href='/track/{{rawurlencode($t->title)}}'>{{$t->title}}</a>
		</div>
	@endforeach
@stop
