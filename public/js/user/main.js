!function(){
require([
		"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js",
		"/js/lib/underscore-min.js",
		"/js/lib/json2-min.js",
	],function(){
require([
		"/js/lib/backbone-min.js",
	], function(){
		var v = 6;
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
		var name = $(".user_name").html();
		var option = $(".option").html();
		var now_loading = $("<p/>").html("ロード中・・・");
		var fail = function(msg){
			wrapper.html("");
			wrapper.append(
				msg || "<p>スコアデータがありません・・・</p>"
			);
		};
		if(!name){
			fail();
			return;
		}
		if(document.form_upload){
			$(".inner_header .upload").click(function(){
				document.form_upload.submit();
			});
		}
		wrapper.append(now_loading);
		console.log(name);
		if(!option){
			option = -1;
		}
		if(option === "0"){
			var json_url = "/user/"+name+"/"+(option)+".json";
			var json_updated_url = "";
		}else{
			var json_url = "/user/"+name+"/"+(parseInt(option)-1)+".json";
			var json_updated_url = "/user/"+name+"/"+option+".json";
		}
		var jsonLoaded = function(json, json_updated){
			console.log(json);
			console.log(json_updated);
			if(!json){
				fail();
				return;
			}
			
			var p = new Profile(
				json.profile
				, {parse: true}
			);
			if(json_updated){
				var p_updated = new Profile(
					json_updated.profile,
					{parse: true}
				);
			}else{
				var p_updated = new Profile();
			}
			var sv = new ScoreViewer({
				profile: p,
				profile_updated: p_updated,
				title: name+"さんのスコアデータ",
				name: name,
				option: option,
				show_play_count: json.show_play_count,
				created_at: json_updated?json_updated.created_at:json.created_at
			});
			var pv = new ScoreViewerView({
				model: sv
			});
			pv.render();
			now_loading.remove();
                	wrapper.append(pv.$el);
		};
		$.getJSON(json_url).complete(function(e){
			var json = e.responseJSON;
			$.getJSON(json_updated_url+(json?"?updated=1&played=1":"")).complete(function(e){
				var json_updated = e.responseJSON;
				if(json){
					jsonLoaded(json, json_updated);
				}else{
					jsonLoaded(json_updated, null);
				}
			});
		});
	};
	$(function(){
		init();
	});
});
});
});
}();