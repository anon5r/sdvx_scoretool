@extends('template')

@section('content')
<h1>説明</h1>
<p>SDVX IIのスコアデータを収集したり公開したりするサイトです。</p>
<p>要望、バグ報告等は<a href="https://twitter.com/quick_4x">@quick_4x</a>までお願いします。</p>
<p><a href="/user/sample">スコア公開サンプル</a></p>
<p><a href="/user/average">登録者平均さん</a></p>
<h1>使い方</h1>
<p>初代と使い方を少し変えました。ご確認ください。</p>
<ol>
<li>以下のブックマークレットをブックマークに登録する<br>
<a href='
javascript:void(!function(d){if(typeof SCORETOOL_DOMAIN !== "undefined"){console.log("Scoretool is already runnig");return -1;}SCORETOOL_DOMAIN = "http://sdvx-s.coresv.com";var s=d.createElement("script");s.type="text/javascript";s.src=SCORETOOL_DOMAIN+"/js/scoretool/bookmarklet_loader.js";d.head.appendChild(s);}(document));
'>スコア収集用ブックマークレット</a>
<ul>
<li>多くのブラウザの場合、リンクをブックマークバーにドラッグアンドドロップすればOKです</li>
<li>Chrome, Firefox, Safariの最新版でのみ動作を確認しています<br>IEでは動きません！</li>
</li>
</ul>
<li><a target="_blank" href="http://p.eagate.573.jp/game/sdvx/ii/p/index.html">SDVX II公式サイト</a>にアクセス</li>
<li>1で登録したブックマークレットを実行する</li>
<li>ロードが終わったら勝手にスコアビューアへ飛びます</li>
</ul>
<p>スコアをアップロードして他人に公開する場合は、<a href="/register">ユーザー登録</a>してください</p>
</ol>
<h1>アプリなどの紹介</h1>
<p>有志の方が作ってくださいました！<br>ありがとうございます！ぜひご活用ください！</p>
<ul>
<li>iPhoneアプリ
<ul>
<li>
<a href="https://itunes.apple.com/jp/app/sdvx-play-supporter/id684074485" target="_blank">SDVX PLAY SUPPORTER</a>
</li>
<li>
<a href="https://itunes.apple.com/jp/app/sdvx-score-tool/id680060271" target="_blank">SDVX score tool</a>
</li>
</ul>
</li>
</ul>
<h1>FAQ</h1>
<ul>
<li>読み込みが遅い<br>
e-amusementのサーバーに負荷をかけるのが目的ではないため、十分な間隔を置いてからアクセスするようになっています。<br>
過去にアクセス負荷によって、使用が禁止されたツールなども存在するため、慎重な設定になっています。ご了承ください。
</li>
<li>毎回全曲読み込むの？<br>
基本的には最近プレーした楽曲から、更新がなくなるまで、必要最低限のデータのみを読み込みます。<br>
ただし、公式サイトの最近プレーした楽曲は20曲までしか表示されないため、更新が20曲を超える場合は全曲読み込みます。
</li>
</ul>
<h1>API</h1>
<ul>
<li>
ユーザーページのURLの末尾に.jsonをつけるとJSON形式でスコアデータを出力します。<br>
<a href="/user/sample.json">例</a>
</li>
<li>
いずれちゃんとした説明をつけますが、とりあえず2点だけ
<ol>
<li>プレイしてない曲の譜面やレベルは出力に含まれますが、プレイ回数やハイスコアは含まれません（プレイ回数0回と未解禁は、要素の有無で判別してください）。</li>
<li>tracksの要素の"id"は勝手につけたidなので数値や順番などに意味はありません。</li>
</ol>
<li>
ユーザーIDの後ろに"/+数字"をつけることで、以前の更新を引けます。<br>
正の数字は、正順に…"/user/id/0.json"は最初の更新データ<br>
負の数字は、逆順に…"/user/id/-1.json"は最新の更新データ<br>
どちらも添字をオーバーすると404が返ります。
</li>
<li>
使用報告や許可は特に必要ないです。
</li>
</li>
</ul>
<h1>追加予定機能など</h1>
<ul>
<del><li>デフォルトの並び順がおかしいから直す</li></del>
<del><li>平均さん</li></del>
<li>他人との比較</li>
<del><li>データの更新履歴</li></del>
</ul>
@stop

