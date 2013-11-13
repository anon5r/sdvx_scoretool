Profile = Backbone.Model.extend({
	initialize: function(){
		console.log("called Profile initialize");
		if(!this.has("tracks")){
			this.set("tracks", new Tracks());
		}
	},
	parse: function(json, op){
		console.log("Profile parse called!");
		var i;
		var json_parsed = {};
		for(i in json){
			json_parsed[i] = json[i];
		}
		if(json_parsed.tracks){
			json_parsed.tracks = new Tracks(json.tracks, {parse: true});
		}
		return json_parsed;
	}
});
Track = Backbone.Model.extend({
	parse: function(json, op){
		var json_parsed = [];
		var i;
		for(i in json){
                        json_parsed[i] = json[i];
                }
		var diffs = ["novice", "advanced", "exhaust", "infinite"];
		for(i=0; i<diffs.length; i++){
			var d = diffs[i];
			var e = json_parsed[d];
			if(e){
				json_parsed[d] = new Effect(e);
			}
		}	
		return json_parsed;
	}
});
Tracks = Backbone.Collection.extend({
	comparator: function(e){
		return -(e.get("id")||3000);
	},
	parse: function(json, op){
                var i;
                var json_parsed = [];
                for(i=0; i<json.length; i++){
                        json_parsed.push(new Track(json[i], {parse:true}));
                }
                return json_parsed;
        },
	getOrCreateTrack: function(title, id){
		var track;
		if(id){
			track = this.findWhere({id: id});
		}else{
			track = this.findWhere({title: title});
		}                                
		if(!track){
			track = new Track({
				title: title
			});
			if(id){
				track.set("id", id);
			}
			this.add(track);
		}
		return track;
	}
});
Effect = Backbone.Model.extend({
});
