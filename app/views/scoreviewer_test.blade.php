@extends('template')
@section('includes')
@parent
<script src="/js_test/scoreviewer/main.js"></script>
<link rel="stylesheet" href="/css/scoreviewer.css">
@stop
@section('tweet_button')
@stop
@section('menu')
@parent
@if(Auth::user())
<form name="form_upload" method="POST" action="/upload" style="display:none;">
<input type=hidden name="profile" value="">
</form>
<a class="upload yellow right">スコアをアップロード</a>
@else
<a class="upload yellow right disable">スコアをアップロード</a>
@endif
@stop
@section('content')
<div class="no_score" style="display:none">
<p>スコアデータがありません</p>
<p>SDVX II公式サイトでブックマークレットを起動して、取得してください</p>
</div>
@stop
