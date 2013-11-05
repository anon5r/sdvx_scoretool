<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>
@section('title')
SDVX Score Tool ver.ii
@show
</title>
@section('includes')
<script src="/js/lib/require.js"></script>
<link rel="stylesheet" href="/css/common.css?v=1">
@show
@section('init_header')
<script>
initHeader = function(){
	var tb = document.getElementById("tweet_button");
	if(tb){
		tb.innerHTML = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'+window.location.href+'">Tweet</a>';
	}
	if(typeof twttr !== "undefined" && twttr){
		twttr.widgets.load()
	}
};
window.onload = initHeader;
</script>
@show
</head>
<body>
<canvas class="bg_canvas" id="bg"></canvas>
<script>
!function(){
    var canvas = document.getElementById("bg");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    var ctx = canvas.getContext("2d");
    var radius_max = Math.sqrt(canvas.width*canvas.width + canvas.height*canvas.height);
    var radius_step = 40;
    var drawGradation = function(x, y){
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        var s = radius_max / radius_step;
        for(var radius = radius_step; radius < radius_max; radius += radius_step){
            ctx.fillStyle = "rgba("+(Math.random()*256|0)+","+(Math.random()*256|0)+","+(Math.random()*256|0)+","+1.0/s+")";
            ctx.beginPath();
            ctx.arc(x, y, radius, 0, Math.PI*2, false);
            ctx.fill();
        }
    }
    drawGradation(0, canvas.height);
}();
</script>
@if(!isset($no_header))
<div class="header">
<div class="inner_header">
@section('header')
<a class="black" href="/">SDVX Score Tool ver.ii</a>
@section('tweet_button')
<div id="tweet_button" class="tweet_button">
</div>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
@show
<div class="menu right">
@section('menu')
@if (Auth::guest())
<a class="green right" href="/login">ログイン</a>
<a class="red right" href="/register">ユーザー登録</a>
@else
<a class="green right" href="/logout">ログアウト</a>
<a class="red right" href="/user/{{Auth::user()->name}}">{{Auth::user()->name}}</a>
@endif
<a class="blue right" href="/scoreviewer">スコアビューア</a>
@show
@section('menu')
</div>
<div class="clear_both"></div>
</div>
</div>
@if(Session::get('message'))
<div class="message">
{{Session::get('message')}}
</div>
@endif
@endif
@section('wrapper')
<div class="content_wrapper">
<div class="inner_content_wrapper">
@yield('content')
</div>
</div>
@show
</body>
</html>
