BackboneWindowProxy = Backbone.Model.extend({
	initialize: function(){
		var t=this;
		var wp = new Porthole.WindowProxy(
			this.get("targetDomain"),
			this.get("windowName")
			);
		wp.addEventListener(function(e){t.trigger("message", e);});
		this.wp = wp;
	},
	post: function(data){
		this.wp.post(data);
	}
});
