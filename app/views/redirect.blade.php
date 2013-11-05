@extends('template')
@section('includes')
@parent
<meta http-equiv="refresh" content="3 ; URL={{$url}}">
@stop
@section('content')
@if(isset($message))
<p>{{$message}}</p>
@endif
<p>3秒後にジャンプします</p>
@stop
