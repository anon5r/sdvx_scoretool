ScoreViewerView = Backbone.View.extend({
	tagName: "div",
	className: "scoreviewer_wrapper",
	initialize: function(){
		this.pt = new ProfileTable({model: this.model});
		this.tt = new TrackTable({model: this.model});
		this.mt = new MedalsTable({model: this.model});
		this.gt = new GradesTable({model: this.model});
	},
	render: function(){
		console.log(this.model);
		this.$el.html("");
		this.pt.render();
		this.mt.render();
		this.gt.render();
		this.tt.render();

		var name = this.model.get("name");
		var option = parseInt(this.model.get("option"));

		var history_selecter = $("<p/>");
                history_selecter.append(
			$("<a/>").attr({href:"/user/"+name+"/"+(option-1)}).html("< 前回の更新")
		);
		
		if(option !== -1){
                	history_selecter.append("　");
			if(option !== -2){
                		history_selecter.append(
					$("<a/>").attr({href:"/user/"+name+"/"+(option+1)}).html("次回の更新 >")
				);
			}else{
                		history_selecter.append(
					$("<a/>").attr({href:"/user/"+name}).html("次回の更新 >")
				);
			}
		}
		if(!this.model.get("local")){
			this.$el.append(history_selecter);
			this.$el.append(
					$("<p/>").html("更新日: "+this.model.get("created_at"))
				       );
		}
		this.$el.append(
                 	$("<h1/>").html(this.model.get("title"))
		);

		this.$el.append(this.pt.$el);
		this.$el.append(this.mt.$el);
		this.$el.append(this.gt.$el);
		this.$el.append($("<div/>").addClass("clear_both"));
		this.$el.append(this.tt.$el);
	}
});
ProfileTable = Backbone.View.extend({
	tagName: "div",
	className: "profile scoreviewer_content",
	title: "Profile",
	render: function(){
		this.$el.html("");
		var m = this.model;
		var p = m.get("profile");
		var table = $("<table/>");
		var thead = $("<thead/>");
		var tbody = $("<tbody/>");
		thead.append(
			$("<tr/>").append(
				$("<th/>")
			).
			append(
				$("<th/>").html(m.get("name")).addClass("you")
			)
		);
		var headers = m.get("profile_row_headers");
		var rows = m.get("profile_rows");
		var i;
		for(i=0; i<rows.length; i++){
			var row = rows[i];
			var tr = $("<tr/>").append(
				$("<th>").
				html(headers[i])
			);
			if(!p.has(row)){
				tr.append(
					$("<td/>").
					html("-")
				);
			}else{
				tr.append(
					$("<td/>").
					html(p.get(row))
				);
			}
			tbody.append(tr);
		}
		table.append(thead);
		table.append(tbody);
		this.$el.append($("<h1/>").html(this.title));
		this.$el.append(table);
	}
});
SumTable = Backbone.View.extend({
	tagName: "div",
	render: function(){
		this.$el.html("");
		var m = this.model;
		var p = m.get("profile");
		var t = p.get("tracks");
		var attr = this.attr;
		var diffs = m.get("diffs");
		var diff_headers = m.get("diff_headers_clipped");
		var rows = m.get(this.rows);
		var headers = m.get(this.headers);
		var effect_count = m.get("effect_count");
		var dummy_e = {"get":function(){}};
		var table = $("<table/>");
		var thead = $("<thead/>");
		var tbody = $("<tbody/>");
		var i;
		var tr = $("<tr>");

		tr.append($("<th/>").html(""));
		for(i=0; i<diff_headers.length; i++){
			tr.append(
				$("<th/>").
				html(diff_headers[i]).
				addClass(diffs[i])
			);
		}
		tr.append($("<th/>").html("ALL").addClass("all"));
		thead.append(tr);

		for(i=0; i<rows.length; i++){
			var tr = $("<tr/>");
			tr.append(
				$("<th/>").
				html(headers[i]).
				addClass(rows[i]).
				addClass(attr)
			);
			var j;
			var rowsum = 0;
			var over_sum = 0;
			for(j=0; j<diffs.length; j++){
				var over = effect_count[j];
				var val = t.map(function(e){
					return e.get(diffs[j])||dummy_e;
				}).filter(function(e){
					return e.get(attr)===rows[i];
				}).length;
				tr.append(
					$("<td/>").
					html(val+"<small>/"+over+"</small>")
				);
				rowsum += val;
				over_sum += over;
			}
			tr.append(
				$("<td/>").
				html(rowsum+"<small>/"+over_sum+"</small>")
			)
			tbody.append(tr);
		}
		table.append(thead);
		table.append(tbody);

		this.$el.append($("<h1/>").html(this.title));
		this.$el.append(table);
	}
});
GradesTable = SumTable.extend({
	rows: "grades_rows",
	className: "grades scoreviewer_content",
	title: "Grade",
	headers: "grades_row_headers",
	attr: "grade"
});
MedalsTable = SumTable.extend({
	rows: "medals_rows",
	className: "medals scoreviewer_content",
	title: "Medal",
	headers: "medals_row_headers",
	attr: "medal"
});
TrackTable = Backbone.View.extend({
	tagName: "div",
	className: "track scoreviewer_content",
	initialize: function(){
		this.listenTo(this.model, "change", this.render);
		this.listenTo(this.model.get("profile").get("tracks"), "sort", this.render);
		this.tts = new TrackTableSelector({model: this.model});
		this.tds = new TrackDiffSelector({model: this.model});
	},
	getSortAllow: function(diff, col){
		var m = this.model;
		return $("<th/>").
			addClass(diff).
			addClass(col).
			append(
				$("<a/>").
				html("▲").
				click(function(){
					m.sortWith(diff, col, 'asc');
				})
			).append("<br>").
			append(
				$("<a/>").
				html("▼").
				click(function(){
					m.sortWith(diff, col, 'desc');
				})
			)
	},
	render: function(){
		console.log("TrackTable reder");
		this.$el.html("");
		var m = this.model;
		var p = m.get("profile");
		var ts = p.get("tracks");
		var diffs = m.get("diffs");
		var diff_headers = m.get("diff_headers");
		var diff_availables = m.get("diff_availables");
		var col_selected = m.get("col_selected");
		var cols = m.get("cols")[col_selected];
		var col_headers = m.get("col_headers")[col_selected];
		var title = m.get("titles")[col_selected];
		var dummy_e = {"get":function(){},"has":function(){}};
		var track_table = $("<table/>");
		var thead = $("<thead/>");
		var tr1 = $("<tr/>").addClass("difficulty");
		var tr_avg = $("<tr/>").addClass("average");
		var tr2 = $("<tr/>");
		var tr_sort = $("<tr/>").addClass("sort");
		tr1.append(
			$("<th/>").
			html("").
			addClass("difficulty").
			attr({width:"230"})

		);
		tr2.append(
			$("<th/>").
			html("TITLE").
			addClass("title").
			attr({rowspan:"2"})
		);
		tr_avg.append(
			$("<th/>").
			html("AVERAGE").
			addClass("average")
		);
		var i, j;
		var cols_available=0;
		for(i=0; i<diff_headers.length; i++){
			if(!diff_availables[i]){
				continue;
			}
			cols_available += 1;
			var diff = diffs[i];
			tr1.append(
				$("<th/>").
				html(diff_headers[i]).
				attr("colspan", col_headers.length).
				addClass(diff).
				attr({width:"170"})
			);
			var tmp_ts = ts.map(function(e){
				return e.get(diff)||dummy_e;
			}).filter(function(e){
				return e.has("highscore");
			});
			var avg = tmp_ts.reduce(function(a,b){return a+b.get("highscore");}, 0) / tmp_ts.length;
			var tmp_ts_pre = ts.map(function(e){
				return e.get(diff)||dummy_e;
			}).filter(function(e){
				return (e.get("previous")||e).has("highscore");
			});
			var avg_pre = tmp_ts_pre.reduce(function(a,b){
				return a+((b.get("previous")||b).get("highscore"));
			}, 0) / tmp_ts_pre.length;
			var avg_diff = avg - avg_pre | 0;
			var th = $("<th/>").
				html(avg|0).
				attr("colspan", col_headers.length).
				addClass(diff);
			if(avg !== 0){
				var span_diff = $("<span/>").addClass("previous");
				if(avg_diff > 0){
					span_diff.html("(+"+avg_diff+")").addClass("plus");
				}else if(avg_diff < 0){
					span_diff.html("("+avg_diff+")").addClass("minus");
				}
				th.append(span_diff);
			}
			tr_avg.append(th);
			for(j=0; j<col_headers.length; j++){
				tr2.append(
					$("<th/>").
					html(col_headers[j]).
					addClass(diff).
					addClass(cols[j])
				);
				tr_sort.append(this.getSortAllow(diff, cols[j]));
			}
		}
		thead.append(tr1);
		thead.append(tr_avg);
		thead.append(tr2);
		thead.append(tr_sort);
		var tbody = $("<tbody/>");
		var t = this;
		p.get("tracks").each(function(e){
			var tr = $("<tr/>");
			var title = e.get("title");
			var id = e.get("id");
			var encoded_title = encodeURIComponent(title);
			var th = $("<th/>").
				append(
					$("<div/>").
					append(
						$("<a/>").
						attr({
							href: "/track/"+encoded_title,
						}).
						html(title)
					).
					addClass("title_wrapper")
				).
				addClass("title");
			tr.append(th);
			var i, j;
			for(i=0; i<diffs.length; i++){
				if(!diff_availables[i]){
					continue;
				}
				var ef = e.get(diffs[i]);
				if(!ef) ef=dummy_e;
				var ef_p = ef.get("previous");
				for(j=0; j<cols.length; j++){
					var col = cols[j];
					var val = ef.get(col);
					val = val === undefined?"-":val;
					
					var td = $("<td/>").
						addClass(diffs[i]).
						addClass(cols[j]);
					td.html(val).addClass(val);
					if(ef_p){
						var val_p = ef_p.get(col);
						var val_diff = val - val_p;
						if(val !== val_p && val_diff !== 0){
							if(!isNaN(val_diff) || val_p){
								var div_prev = $("<div/>").addClass("previous "+cols[j]);
								if(isNaN(val_diff)){
									div_prev.html("("+val_p+")").addClass(val_p);
								}else{
									if(val_diff>0){
										div_prev.html("+"+val_diff).addClass("plus");
									}else{
										div_prev.html(val_diff).addClass("minus");
									}
								}
								td.append(div_prev);
							}
							td.addClass("updated");
						}
					}

					tr.append(td);
				}
			}
			tbody.append(tr);
		});
		var table_width_css = {width:(cols_available>0?240+180*cols_available:420)+"px"};
		track_table.css(table_width_css);
		this.$el.css(table_width_css);

		track_table.append(thead);
		track_table.append(tbody);

		this.tts.render();
		this.tds.render();
		this.$el.append(this.tts.$el);
		this.$el.append(this.tds.$el);
		this.$el.append($("<h1/>").html(title).addClass(col_selected));
		this.$el.append(track_table);
	}
});
TrackTableSelector = Backbone.View.extend({
	tagName: "div",
	className: "table_selector",
	render: function(){
		this.$el.html("");
		var m = this.model;
		var col_selected = m.get("col_selected");
		var col_groups = m.get("col_groups");
                var titles_clipped = m.get("titles_clipped");
		var i;
		for(i=0; i<col_groups.length; i++){
			var col = col_groups[i];
			var a = $("<a/>").addClass("table_selector_button");
			a.html(titles_clipped[col]);
			a.addClass(col);
			if(col === col_selected){
				a.addClass("selected");
			}else{
				!function(){
					var c = col;
					a.click(function(){
						m.switchColSelectedTo(c);
					});
				}();
			}
			this.$el.append(a);

		}
		this.$el.append($("<div/>").addClass("clear_both"));
	}
});
TrackDiffSelector = Backbone.View.extend({
	tagName: "div",
	render: function(){
		this.$el.html("");
		this.$el.removeClass().addClass("diff_selector");

		var m = this.model;

		var diffs = m.get("diffs");
		var diff_availables = m.get("diff_availables");
		var diff_headers = m.get("diff_headers_clipped");
		
		var col_selected = m.get("col_selected");

		var i;
		for(i=0; i<diffs.length; i++){
			var diff = diffs[i];
			var available = diff_availables[i];	
			var a = $("<a/>");
			a.addClass("table_selector_button").
				addClass(diff).
				html(diff_headers[i]);
			if(!available){
				a.addClass("disable");
			}
			!function(){
				var k=i;
				a.click(function(){
					m.switchDiffAvailables(k);
				});
			}();
			this.$el.append(a);
		}
		this.$el.append($("<div/>").addClass("clear_both"));
		this.$el.addClass(col_selected);

		//this.$el.html("test div");
	}
});