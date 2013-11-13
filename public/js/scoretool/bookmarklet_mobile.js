require([
		"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js",
		SCORETOOL_DOMAIN+"/js/lib/underscore-min.js",
		SCORETOOL_DOMAIN+"/js/lib/json2-min.js",
	],function(){
require([
		SCORETOOL_DOMAIN+"/js/lib/backbone-min.js",
		SCORETOOL_DOMAIN+"/js/lib/porthole.min.js"
	], function(){
require([
		SCORETOOL_DOMAIN+"/js/models/Profile.js",
		SCORETOOL_DOMAIN+"/js/models/BackboneWindowProxy.js"
	], function(){
	ScoreLoader = Backbone.Model.extend({
		diffs: ["novice", "advanced", "exhaust", "infinite"],
		skills: ["-", "岳翔","流星","月衝","瞬光","天極","烈風","雷電","麗華","魔騎士","剛力羅"],
		initialize: function(){
			this.profile = null;
			this.title2id = {};
			var t = this;
			$.getJSON(SCORETOOL_DOMAIN+"/title2id.json?callback=?", function(data){
				t.title2id = data;
			});
			this.set({
				state: "idle",
				forceLoading: false
			});
		},
		startLoading: function(profile_json){
			var t= this;
			if(this.get("state") !== "loading"){
				this.htmlfetcher = new HTMLFetcher();
				this.profile = new Profile(profile_json, {parse: true});
				this.profile.get('tracks').each(function(e){
					var id = t.title2id[e.get("title")];
					if(id){
						e.set({id: id});
					}
				});
				this.set({
					state: "loading"
				});
				this._loadProfile();
			}
		},
		_loadProfile: function(){
			var t=this;
			this.htmlfetcher.getProfileHTML(function(html){
				try{
					if(!html){
						t._error();
						return;
					}
					var el = $(html);
					var name = $("div #playername .name_str", el).html();
					var play_count = $("div #playnum .profile_cnt", el).html().match(/([\d]*)/)[1];
					var skill_lv_m = $("div #skil .profile_skill", el).attr("id").match(/_([\d]+)/);
					var skill = t.skills[0];
					if(skill_lv_m){
						var skill_lv = skill_lv_m[1];
						//skill = "Lv"+skill_lv+" "+t.skills[parseInt(skill_lv)];
						var skill_name = $(".profile_skillname", el).html().replace(/ /g,"");
						skill = "Lv"+skill_lv+" "+skill_name;
					}
					var packet = $("div #packet .profile_cnt", el).html().match(/([\d]*)/)[1];
					var block = $("div #block .profile_cnt", el).html().match(/([\d]*)/)[1];
					t.profile.set({
						name: name,
						play_count: parseInt(play_count),
						skill: skill,
						packet: parseInt(packet),
						block: parseInt(block)
					});
					t.trigger("load_profile", name);
					t._loadTrackDetail();
				}catch(e){
					t._error();
					return;
				}
			});
		},
		_loadPageAux: function(html){
			var t=this;
			var lis = $("li.data_col", html);
                        var i;
			if(html.match(/ベーシックコースに加入/)){
				throw Exception;
			}
                        for(i=0; i<lis.length; i++){
                                var li = lis.eq(i);
                                var title = $(".title a",li).html();
                                var id = t.title2id[title];
                                var artist = $(".artist",li).html();
        
	                        console.log(title,", "+artist+", "+id);
        
				var tracks = t.profile.get("tracks");
	                        //search with id
                                //if not found, make it and push into collection
				var track = tracks.getOrCreateTrack(title, id);
				track.set("artist", artist);
                                var tds = $("tr td",li);
                                var j;
                                for(j=1; j<tds.length; j++){
                                        var effect_attr = {};
                                        var td = tds.eq(j);
                                        var difficulty = td.attr("class");
					var td_html = td.html();
                                        if(td_html){
						var m_medal = td_html.match(/playdata\/(.*?)_icon\.jpg/);
						var m_grade = td_html.match(/grade_(.*)\.jpg/);
						if(m_medal){
							effect_attr["medal"] = m_medal[1];
						}
						if(m_grade){
							effect_attr["grade"] = m_grade[1];
						}
                                        }
					
					if(track.has(difficulty)){
						track.get(difficulty).set(effect_attr);
					}else{
                                        	track.set(difficulty, new Effect(effect_attr));
					}
                                }
                        }
		},
		_loadRecentPage: function(){
			var t=this;
			this.htmlfetcher.getNextRecentPageHTML(function(html){
				try{
					if(!html){
						t._loadTrackPage();
						return;
					}
					t._loadPageAux(html);
					t.trigger("add_recent_page", {
						now: t.htmlfetcher.recent_page-1,
						max: t.htmlfetcher.recent_page_max
					});
					t._loadTrackDetail();
				}catch(e){
					t._error();
					return;
				}
			});
		},
		_loadTrackPage: function(){
			var t=this;
			this.set("forceLoading", true);
                        this.htmlfetcher.getNextTrackPageHTML(function(html){
				try{
                                	if(!html){
						t._endLoading();
                                        	return;
                                	}
					t._loadPageAux(html);
					t.trigger("add_track_page", {
						now: t.htmlfetcher.track_page-1,
						max: t.htmlfetcher.track_page_max
					});
                                	t._loadTrackDetail();
				}catch(e){
					t._error();
					return;
				}
                        });
		},	
		_loadTrackDetail: function(){
			var t=this;
			var saved_url=this.htmlfetcher.tracks_cue[0];
                        this.htmlfetcher.getNextTrackDetailHTML(function(html){
				try{
                                	if(!html){
						t._loadRecentPage();
                                        	return;
                                	}
					console.log("load track detail");
					var title = $("#music_info #music_title", html).html();
					if(!title){
						t.htmlfetcher.tracks_cue.push(saved_url);
						t._loadTrackDetail();
						return;
					}
					var id = t.title2id[title];
					var tracks = t.profile.get("tracks");
					var track = tracks.getOrCreateTrack(title, id);
					var divs = $(".music_box", html);
					var i;
					var updated = false;
					for(i=0; i<divs.length; i++){
						var div = divs.eq(i);
						if(!$(".level", div)[0]){
							continue;
						}
						var difficulty = div.attr("id");
						var effect = track.get(difficulty);
						if(!effect){
							effect = new Effect();
							updated = true;
						}
						var level = $(".level", div).html();
						var effected_by = $(".effect", div).html().match(/effected by (.*)$/)[1];
						var illustrated_by = $(".illust", div).html().match(/illustrated by (.*)$/)[1];
						effect.set({
							level: parseInt(level),
							effected_by: effected_by,
							illustrated_by: illustrated_by
						});
						var cnts = $("ul li .cnt", div);
						var rows = ["highscore", "play_count", "comp_count", "uc_count", "per_count"];
						var j;
						for(j=0; j<rows.length; j++){
							var attr = rows[j];
							var val = cnts.eq(j).html().match(/([\d]*)/)[1];
							if(!val){
								continue;
							}
							if(effect.get(attr) === parseInt(val)){
								continue;
							}
							effect.set(attr, parseInt(val));
							updated = true;
						}
					}
					t.trigger("add_track", track);
                                	console.log(track);
					if(!t.get("forceLoading") && !updated){
						t._endLoading();
					}else{
						t._loadTrackDetail();
					}
				}catch(e){
					t._error();
					return;
				}
                        });
		},
		_error: function(){
			console.log("some error occured while loading");
			this.set({
				state: "error"
			});
		},
		_endLoading: function(){
			var diffs = this.diffs;
			this.profile.get("tracks").each(function(e){
				var i;
				for(i=0; i<diffs.length; i++){
					var d = diffs[i];
					var ef = e.get(d);
					if(ef){
						if(!ef.has("level")){
							e.unset(d);
						}
					}
				}
			});
			console.log("load has ended Succesfully");
			this.set({
                                state: "end",
				forceLoading: false
                        });
		}
	});
	HTMLFetcher = Backbone.Model.extend({
		RETRY_MAX: 10,
		WAIT_TIME: 2000,
		PROFILE_URL: "http://p.eagate.573.jp/game/sdvx/ii/p/profile/",
		TRACK_DATA_URL: "http://p.eagate.573.jp/game/sdvx/ii/p/playdata/",
		recent_page: -1,
		recent_page_max: -1,
		track_page: -1,
		track_page_max: -1,
		tracks_cue: null,
		initialize: function(){
			this.tracks_cue = [];
		},
		_getAndSetXHR: function(url, callback, retry_n){
			console.log(url);
			var t=this;
			if(typeof retry_n === "undefined"){
				retry_n = 0;
			}
			if(retry_n > this.RETRY_MAX){
				callback(null);
				return;
			}
			var retry = function(){
				t._getAndSetXHR(url, callback, retry_n+1);
			}
			var xhr = new XMLHttpRequest();
			xhr.open("GET", url, true);
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					console.log("http status:"+xhr.status);
					if(xhr.status == 200){
						var html = xhr.response.
							replace(/src *?=/g,"ssrc=").
							replace(/href=".*?\.css"/g, "");
						$("#load_div").html(html);
						callback(html);
					}else{
						retry();
					}
				}
			}
			setTimeout(function(){
				xhr.send(null);
			}, this.WAIT_TIME);
			return xhr;
		},
		getProfileHTML: function(callback){
			this._getAndSetXHR(this.PROFILE_URL, function(html){
				if(!html){
					callback(null);
				}else{
					callback(html);
				}
			});
		},
		_getNextPageHTMLAux: function(url, callback, func){
			var t = this;
                        this._getAndSetXHR(url, function(html){
                                if(!html){
                                        callback(null);
                                }else{
					func(html);
					var el = $(html);
                                       var as = $("li.data_col div.title a", el);
                                       var i,track_url;
                                       for(i=0; i<as.length; i++){
												attrNode=as[i].getAttributeNode('onclick');
										  		if(attrNode != null) {
                                                	track_url = attrNode.value.match(/\(\'(.*)\'\)/)[1];
										   		}else{
										  			// retry for mobile
										   			attrNode=as[i].getAttributeNode('href');
										   			track_url=attrNode.value;
										   		}
												if(typeof attrNode == "undefined"|| attrNode == null)
                                        			continue;
                                                if(track_url==null)
                                             		continue;
                                                t.tracks_cue.push(track_url);
                                                console.log(track_url);
                                       }
                                       callback(html);
                                }
                        });
		},
		getNextRecentPageHTML: function(callback){
			if(this.recent_page > this.recent_page_max){
				callback(null);
				return;
			}
			if(this.recent_page < 0){
                                this.recent_page = 1;
                        }
			var url = this.TRACK_DATA_URL+"?sort=26&page="+this.recent_page;
			var t = this;
			this._getNextPageHTMLAux(url, callback, function(html){
				if(t.recent_page_max < 0){
                                        var page_max = $(".page>a span", html).last().html();
                                        t.recent_page_max = page_max || 1;
                                }
			});
			this.recent_page ++;
		},
		getNextTrackPageHTML: function(callback){
                        if(this.track_page > this.track_page_max){
                                callback(null);
                                return;
                        }
                        if(this.track_page < 0){
                                this.track_page = 1;
                        }
                        var url = this.TRACK_DATA_URL+"?sort=0&page="+this.track_page;
                        var t = this;
                        this._getNextPageHTMLAux(url, callback, function(html){
                                if(t.track_page_max < 0){
                                        var page_max = $(".page>a span", html).last().html();
					t.track_page_max = page_max || 1;
                                }
                        });
                        this.track_page ++;
                },
		getNextTrackDetailHTML: function(callback){
			var url = this.tracks_cue.shift();
			if(!url){
				callback(null);
				return 0;
			}
                        this._getAndSetXHR(url, function(html){
                                if(!html){
                                        callback(null);
                                }else{
                                        callback(html);
                                }
                        });
                }
	});
	var ScoreLoaderWatcher = Backbone.Model.extend({
		initialize: function(){
			var t = this;
			var bwp = this.get("bwp");
			var model = this.get("model");
			this.listenTo(model, "change:state", function(){
				console.log("state changed");
				switch(model.get("state")){
					case "loading":
						t.sendMsg("ローディングを開始しました");
						break;
					case "end":
						t.sendMsg("ローディングを終了しました");
						bwp.post({
							type: "profile",
							value: JSON.stringify(model.profile)
						});
                                                break;
					case "error":
						t.sendMsg("ローディング中にエラーが発生しました");
						bwp.post({type: "error"});
                                                break;
				}
			});
			this.listenTo(model, "change:forceLoading", function(){
				switch(model.get("forceLoading")){
					case true:
						t.sendMsg("強制全曲ロードを開始します");
						model.profile.set({tracks: new Tracks()});
						console.log(model);
						break;
				}
			});
			this.listenTo(model, "add_track", function(e){
				t.sendMsg(e.get("title")+"を読み込みました");
			});
			this.listenTo(model, "add_track_page", function(e){
				t.sendMsg("全楽曲の("+e.now+"/"+e.max+")ページ目を読み込みました");
			});
			this.listenTo(model, "add_recent_page", function(e){
                                t.sendMsg("最近プレーした楽曲の("+e.now+"/"+e.max+")ページ目を読み込みました");
                        });
			this.listenTo(model, "load_profile", function(e){
                                t.sendMsg("ユーザープロフィールを読み込みました");
                        });
			this.bwp = bwp;
		},
		sendMsg: function(value){
			console.log(value);
			console.log(this);
			this.bwp.post({type:"msg", value:value});
		}

	});
	var IFRAME_NAME = "ifServer";
	var SCORETOOL_URL = SCORETOOL_DOMAIN+"/scoretool";
	var init = function(){
		var body = $("body");
		var cssFullScreen = {
			position: "fixed",
                        width: "100%",
                        height: "100%",
                        top: "0px",
                        left: "0px"
		}
		var overlay = $("<div/>").
			attr({
				id: "scoretool_overlay"
			}).
			css(cssFullScreen).
			css({
				background: "rgba(0,0,0,0.7)",
				opacity: "0"
			});
		var ifServer = $("<iframe/>").
			attr({
				id: IFRAME_NAME,
				name: IFRAME_NAME,
				src: SCORETOOL_URL
			}).
			css(cssFullScreen).
			css({
				border: "0px",
				opacity: "0"
			});
		var load_div = $("<div/>").
                        attr({
                                id: "load_div"
                        }).
                        css(cssFullScreen);
		body.append(load_div);
		body.append(overlay);
		body.append(ifServer);
		body.css("overflow", "hidden");
		$("#scoretool_overlay").animate({opacity:"1"});
		var bwp = new BackboneWindowProxy({
			targetDomain: SCORETOOL_URL, 
			windowName: "ifServer"
		});
		 sl = new ScoreLoader();
	
		 slw = new ScoreLoaderWatcher({model: sl, bwp: bwp});
		bwp.on("message", function(e){
			console.log("receive by bookmarklet");
			console.log(e);
			if(e.data.type === "eval"){
				eval(e.data.value);
			}
		});
		startConnectingIframe(bwp, function(){
                        ifServer.animate({opacity:"1"});
		});
	};
	var startConnectingIframe = function(bwp, callback){
		var intervalPost = setInterval(function(){
			bwp.post({type:"ACK"});
		}, 500);
		var checkSYN = function(e){
			if(e.data.type === "SYN"){
                                clearInterval(intervalPost);
                                callback();
                                bwp.off(checkSYN);
                        }
		};
		bwp.on("message", checkSYN);
	};
	init();
});
});
});