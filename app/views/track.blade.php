@extends('template')
@section('title')
{{$title}} のトラックデータ - SDVX Score Tool ver.ii
@stop
@section('includes')
@parent
<div id="track_title" class="track_title" style="display:none">{{$title}}</div>
@if(isset($option))
<div class="option" style="display:none">{{$option}}</div>
@endif
<script src="/js/track/main.js"></script>
<link rel="stylesheet" href="/css/scoreviewer.css">
@stop
@section('init_header')
<script>
initHeader = function(){
        var tb = document.getElementById("tweet_button");
        if(tb){
		var title = document.getElementById("track_title").innerHTML;
		tb.innerHTML = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://sdvx-s.coresv.com/track/'+encodeURIComponent(title).replace(/\./g, "%2e")+'">Tweet</a>';
        }
        if(twttr){
                twttr.widgets.load()
        }
};
window.onload = initHeader;
</script>
@stop
@section('tweet_button')
@parent
@stop
@section('menu')
@parent
@stop
@section('content')
@stop
