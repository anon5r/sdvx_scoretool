require([
		"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js",
		"/js/lib/underscore-min.js",
		"/js/lib/json2-min.js",
	],function(){
require([
		"/js/lib/backbone-min.js",
	], function(){
		var v = 2;
                requirejs.config({
                        urlArgs: "v=" + v
                });
require([
		"/js/models/Profile.js",
		"/js/models/ScoreViewer.js",
		"/js/views/ScoreViewer.js"
	], function(){
	var init = function(){
		var wrapper = $(".inner_content_wrapper");
		if(!localStorage["scoretool_profile"]){
			$(".upload").addClass("disable");
			$(".no_score").css({display:"block"});
			return;
		}
		var p = new Profile(
			JSON.parse(localStorage["scoretool_profile"])
			, {parse: true}
		);
		var sv = new ScoreViewer({
			profile: p,
			title: "ローカルスコアビューア",
			name: "YOU",
			show_play_count: true,
			local: true
		});
		var pv = new ScoreViewerView({
			model: sv
		});
		pv.render();
                wrapper.append(pv.$el);
		if(document.form_upload){
			$(".inner_header .upload").click(function(){
				document.form_upload.profile.value = localStorage["scoretool_profile"];
				document.form_upload.submit();
				$(".inner_header .upload").unbind('click');
			});
		}
	};
	$(function(){
		init();
	});
});
});
});
