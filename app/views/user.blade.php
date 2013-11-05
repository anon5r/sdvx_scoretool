@extends('template')
@section('title')
{{$name}} さんのスコアデータ - SDVX Score Tool ver.ii
@stop
@section('includes')
	<META http-equiv="Pragma" content="no-cache">
	@parent
	<div id="user_name" class="user_name" style="display:none">{{$name}}</div>
	@if($user)
		<div id="login_name" class="login_name" style="display:none">{{$user->name}}</div>
	@endif
	@if(isset($option))
		<div class="option" style="display:none">{{$option}}</div>
	@endif
	@if(isset($_GET['testmode']))
		<script src="/js_test/user/main.js"></script>
	@else
		<script src="/js/user/main.js"></script>
	@endif
	<link rel="stylesheet" href="/css/scoreviewer.css">
@stop
@section('init_header')
<script>
initHeader = function(){
        console.log("init");
        var tb = document.getElementById("tweet_button");
        if(tb){
                tb.innerHTML = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'+window.location.href+'">Tweet</a>';
        }
        if(typeof twttr !== "undefined" && twttr){
                twttr.widgets.load()
        }
        var spc = document.getElementById("show_play_count");
        var user_name = document.getElementById("user_name");
        var login_name = document.getElementById("login_name");
        if(spc && user_name && login_name && user_name.innerHTML === login_name.innerHTML){
                spc.style["display"] = "block";
        }else{
                spc.style["display"] = "none";
        }
};
window.onload = initHeader;
</script>
@stop
@section('menu')
@parent
@if($show_play_count)
<form  name="form_upload" method="POST" action="/upload" style="display:none;">
<input type=hidden name="show_play_count" value="0">
</form>
<a id="show_play_count" class="upload yellow right" style="display:none;">プレイ回数を非公開にする</a>
@else
<form  name="form_upload" method="POST" action="/upload" style="display:none;">
<input type=hidden name="show_play_count" value="1">
</form>
<a id="show_play_count" class="upload yellow right" style="display:none;">プレイ回数を公開する</a>
@endif
@stop
@section('content')
@stop
