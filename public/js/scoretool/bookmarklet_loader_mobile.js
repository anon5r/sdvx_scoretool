(function(){
	var h=window.location.href;
	console.log("run at "+h);
	//if(!h.match(/http:\/\/p\.eagate\.573\.jp\/game\/sdvx\/ii\/p\/playdata\//)){
	if(!h.match(/http:\/\/p\.eagate\.573\.jp/)){
		console.log("Invalid Location");
		window.alert("ブックマークレットの実行場所が違います！");
		return -1;
	}
	var loadJs = function(src, callback){
		var s=document.createElement("script");
		s.type="text/javascript";
		s.onload = callback;
		s.src=src;
		document.head.appendChild(s);
	};
	loadJs(SCORETOOL_DOMAIN+"/js/lib/require.js", function(){
		var v=6;
		loadJs(SCORETOOL_DOMAIN+"/js/scoretool/bookmarklet_mobile.js?v="+v);
	});
})();

