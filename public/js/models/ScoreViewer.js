ScoreViewer = Backbone.Model.extend({
	order: {
		medal: {
			"crash": 10, 
			"play": 20, 
			"comp": 30, 
			"uc": 40, 
			"per": 50
		},
		grade: {
			"d": 10, 
			"c": 20, 
			"b": 30, 
			"a": 40, 
			"aa": 50, 
			"aaa": 60
		}
	},
	initialize: function(){
		this.set({
			diffs: ["novice", "advanced", "exhaust", "infinite"],
			diff_headers: ["NOVICE", "ADVANCED", "EXHAUST", "INFINITE"],
			diff_headers_clipped: ["NOV", "ADV", "EXH", "INF"],
			diff_availables: [true, true, true, true],
			profile_rows: ["name", "play_count", "skill", "packet", "block"],
			profile_row_headers: ["NAME", "PLAY COUNT", "SKILL", "PACKET", "BLOCK"],
			medals_rows: ["per", "uc", "comp", "crash"],
			medals_row_headers: ["PER", "UC", "COMP", "CRASH"],
			grades_rows: ["aaa", "aa", "a", "b", "c", "d"],
			grades_row_headers: ["AAA", "AA", "A", "B", "C", "D"],
			col_selected: "score",
			cols: {
				score: ["level", "medal", "grade", "highscore"],
				count: ["play_count", "comp_count", "uc_count", "per_count"]
			},
			col_headers: {
				score: ["LV", "MDL", "GRD", "SCORE"],
				count: ["PLAY", "COMP", "UC", "PER"]
			},
			titles_clipped: {
				score: "SCORE",
				count: "COUNT"
			},
			titles: {
				score: "Score Data",
				count: "Play Count Data"
			}
		});
		if(!this.has("show_play_count")){
			this.set({show_play_count: true});
		}
		if(this.get("show_play_count")){
			this.set({
				col_groups: ["score", "count"]
			});
		}else{
			this.set({
				col_groups: ["score"]
			});
		}
		var p = this.get("profile");
		if(p){
			var diffs = this.get("diffs");
			var effect_count = [];
			var i;
			var ts = p.get("tracks");
			var p_up = this.get("profile_updated");
			if(p_up){
				p_up.get("tracks").each(function(t){
					var i;
					var t_t = ts.findWhere({id: t.get("id")});
					if(!t_t) return;
					for(i=0; i<diffs.length; i++){
						var d = diffs[i];
						var e = t.get(d);
						if(e){
							e.set("previous", t_t.get(d));
							t_t.set(d, e);
							console.log(t_t);
						}
					}
				});
				for(i in p_up.attributes){
					if(i === "tracks") continue;
					p.set(i, p_up.get(i));
				}
			}else{
				this.set({profile_updated: new Profile()});
			}
			for(i=0; i<diffs.length; i++){
				effect_count[i] = ts.filter(function(e){return e.get(diffs[i])}).length;
			}
			this.set({effect_count: effect_count});
		}
	},
	switchDiffAvailables: function(k){
		var das = this.get("diff_availables");
		das[k] = !das[k];
		this.set({diff_availables: das});
		this.trigger("change");
	},
	switchColSelectedTo: function(col){
		if(!this.get("show_play_count") && col === "count"){
			return;
		}
		this.set({col_selected: col});
	},
	sortWith: function(diff, col, type){
		var tracks = this.get("profile").get("tracks");
		var order=this.order;
		if(type === 'asc'){
			tracks.comparator = function(e){
				var e = e.get(diff);
				if(e){
					var val = e.get(col);
					if(val){
						return order[col]?order[col][val]:val;
					}
				}
				return 10000001;
			}
		}else if(type === 'desc'){
			tracks.comparator = function(e){
				var e = e.get(diff);
				if(e){
					var val = e.get(col);
					if(val){
						return 10000001-(order[col]?order[col][val]:val);
					}
				}
				return 10000001;
			}
		}
		tracks.sort();
	}
});