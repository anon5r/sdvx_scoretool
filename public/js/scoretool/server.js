require([
		"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js",
		"/js/lib/underscore-min.js",
		"/js/lib/json2-min.js",
	],function(){
require([
		"/js/lib/backbone-min.js",
		"/js/lib/porthole.min.js"
	], function(){
require([
		"/js/models/Profile.js",
		"/js/models/BackboneWindowProxy.js"
	], function(){
	Scoretool = Backbone.Model.extend({
		initialize: function(){
			this.reloadProfile();
			this.set({
				scene: "title",
				scoreloader_available: false
			});
			this.bwp = this.get("backborn_window_proxy");
			this.listenTo(this.bwp, "message", this.onMessage);
			console.log(this.profile_original);	
		},
		reloadProfile: function(){
			if(!localStorage){
                                console.log("localStorage Not Found");
                                return
                        }
                        var profile_json = localStorage["scoretool_profile"];
                        if(!profile_json){
                                profile_json = null;
                                this.set({profile_available:false});
                        }else{
                                this.set({profile_available:true});
                        }
			this.profile_original = new Profile(
                                JSON.parse(profile_json),
                                {parse: true}
                        );
		},
		onMessage: function(e){
			var wp = e.source;
                        switch(e.data["type"]){
                                case "ACK":
                                        console.log("get ACK");
                                        wp.post({type:"SYN"});
                                        this.set({scoreloader_available:true});
					this.startLoading();
                                        break;
				case "profile":
					console.log("set new profile");
					localStorage["scoretool_profile"] = e.data.value;
					wp.post({type:"eval", value:"window.location.href=\"http://sdvx-s.coresv.com/scoreviewer\";"})
					this.reloadProfile();
					this.set({scoreloader_available: true});
					break;
				case "error":
					console.log("catch error by secver");
					this.reloadProfile();
					this.set({scoreloader_available: true});
					break;
                                default:
                                        break;
                        }	
		},
		startLoading: function(){
			this.bwp.post({
				type:"eval", 
				value:"sl.startLoading("+JSON.stringify(this.profile_original)+");"
			});
			this.set({scene: "loading", scoreloader_available: false});
		},
		switchToForceLoading: function(){
			this.bwp.post({
                                type:"eval",
                                value:"sl.set({forceLoading:true});"
                        });
		}
	});
	ScoretoolView = Backbone.View.extend({
		className: "scoretool_menu",
		initialize: function(){
			this.title = new ScoretoolTitle({model:this.model});
			this.buttons = new ScoretoolButtons({model:this.model});
			this.logger = new BWPLogger({model:this.model});
			this.listenTo(this.model, "change:scene", this.render);
		},
		render: function(){
			this.$el.html("");
			this.title.render();
                        this.buttons.render();
                        this.$el.append(this.title.$el);
                        this.$el.append(this.buttons.$el);
			this.logger.render();
			this.$el.append(this.logger.$el);
		}
	});
	ScoretoolTitle = Backbone.View.extend({
		tagName: "h1",
		className: "scoretool_title",
		initialize: function(){
			this.listenTo(this.model, "change:scene", this.render);
			this.render();
			if(window === parent){
                                this.$el.addClass("direct_access");
                        }
		},
		render: function(){
			this.$el.html("SDVX Score Tool");
		}
	});
	ScoretoolButtons = Backbone.View.extend({
		tagName: "span",
		className: "scoretool_content",
		initialize: function(){
			this.listenTo(
				this.model, 
				"change:profile_available change:scoreloader_available", 
				this.render
			);
			this.render();
		},
		render: function(){
			var t = this;
			var bt_load = $("<button/>").addClass("press_button").html("全曲読み込みをする");
			bt_load.click(function(){
				t.model.switchToForceLoading();
				bt_load.attr("disabled",true);
			});
			this.$el.html("");
			this.$el.append(
				$("<p/>").html("画像を読まないようにしたので、<br>読み込み中にレイアウトが崩れますが、正常です＞＜").
				css({color:"#AAFFAA"})
			);
			this.$el.append(bt_load).append("<span> <- スコアがおかしくなったら押してください</span>");
		}
	});
	BWPLogger = Backbone.View.extend({
		tagName: "div",
		className: "bwp_logger",
		initialize: function(){
			this.listenTo(this.model.bwp, "message", this.render);
			if(window === parent){
                                this.$el.addClass("direct_access");
                        }
		},
		render: function(e){
			if(!e){
				//this.$el.html("");
			}else if(e.data.type === "msg"){
				console.log(e.data.value);
				this.$el.prepend($("<p/>").html(e.data.value));
			}
		}
	});
	var init = function(){
		var bwp = new BackboneWindowProxy({
                        targetDomain:"http://p.eagate.573.jp/game/sdvx/ii/p/playdata/index.html"
			});
		bwp.on("message", function(e){
                        console.log("receive by server");
                        console.log(e);
                });
                var st = new Scoretool({backborn_window_proxy: bwp});
		var stv = new ScoretoolView({model:st});
		stv.render();
		var wrapper = $(".content_wrapper");
                wrapper.append(stv.$el);
	};
	$(function(){
		init();
	});
});
});
});