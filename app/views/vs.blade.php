@extends('template')
@section('title')
{{$name}} vs. {{$name2}} - SDVX Score Tool ver.ii
@stop
@section('includes')
@parent
<div class="user_name" style="display:none">{{$name}}</div>
<div class="user_name2" style="display:none">{{$name2}}</div>
@if(isset($option))
<div class="option" style="display:none">{{$option}}</div>
@endif
<script src="/js/vs/main.js"></script>
<link rel="stylesheet" href="/css/scoreviewer.css">
@stop
@section('menu')
@parent
@stop
@section('content')
@stop
