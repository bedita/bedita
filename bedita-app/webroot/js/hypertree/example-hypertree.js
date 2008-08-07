function init() {

  var Log = {
	elem: $('log'),
	write: function(text) {
		this.elem.setHTML(text);
	}
  };
	Infovis.initLayout();
	var json ={"id":"347_0","name":"Section 1","children":[{"id":"126510_1","name":"Section 36","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"52163_2","name":"Section 2","data":[{"key":"Section 36","value":"appartenente"}],"children":[]},{"id":"324134_3","name":"Section 3","data":[{"key":"Section 36","value":"appartenente"}],"children":[]}]},{"id":"173871_4","name":"Section 4","data":[{"key":"Section 1","value":"appartenente"}],"children":[]},{"id":"235952_5","name":"Section 5","data":[{"key":"Section 1","value":"appartenente"}],"children":[]},{"id":"235951_6","name":"Section 6","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"2382_7","name":"Section 7","data":[{"key":"Section 6","value":"appartenente"}],"children":[]},{"id":"2415_8","name":"Section 8","data":[{"key":"Section 6","value":"appartenente"}],"children":[]},{"id":"3963_9","name":"Section 9","data":[{"key":"Section 6","value":"appartenente"}],"children":[]},{"id":"7848_10","name":"Section 10","data":[{"key":"Section 6","value":"appartenente"}],"children":[]}]},{"id":"235950_11","name":"Section 11","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"1007_12","name":"Section 12","data":[{"key":"Section 11","value":"appartenente"}],"children":[]},{"id":"327924_13","name":"Section 13","data":[{"key":"Section 11","value":"appartenente"}],"children":[]}]},{"id":"2396_14","name":"Section 14","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"3963_15","name":"Section 9","data":[{"key":"Section 14","value":"appartenente"}],"children":[]},{"id":"32247_16","name":"Section 15","data":[{"key":"Section 14","value":"appartenente"}],"children":[]},{"id":"83761_17","name":"Section 16","data":[{"key":"Section 14","value":"appartenente"}],"children":[]},{"id":"133257_18","name":"Section 17","data":[{"key":"Section 14","value":"appartenente"}],"children":[]}]},{"id":"36352_19","name":"Section 18","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"1013_20","name":"Section 19","data":[{"key":"Section 18","value":"appartenente"}],"children":[]},{"id":"3963_21","name":"Section 9","data":[{"key":"Section 18","value":"appartenente"}],"children":[]},{"id":"5752_22","name":"Section 20","data":[{"key":"Section 18","value":"appartenente"}],"children":[]},{"id":"33602_23","name":"Section 21","data":[{"key":"Section 18","value":"appartenente"}],"children":[]},{"id":"40485_24","name":"Section 22","data":[{"key":"Section 18","value":"is person"}],"children":[]},{"id":"133257_25","name":"Section 17","data":[{"key":"Section 18","value":"appartenente"}],"children":[]}]},{"id":"236021_26","name":"Section 23","data":[{"key":"Section 1","value":"appartenente"}],"children":[]},{"id":"236024_27","name":"Section 24","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"909_28","name":"Section 25","data":[{"key":"Section 24","value":"appartenente"}],"children":[]},{"id":"237377_29","name":"Section 26","data":[{"key":"Section 24","value":"is person"}],"children":[]}]},{"id":"235953_30","name":"Section 27","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"1440_31","name":"Section 28","data":[{"key":"Section 27","value":"appartenente"}],"children":[]}]},{"id":"235955_32","name":"Section 29","data":[{"key":"Section 1","value":"appartenente"}],"children":[{"id":"909_33","name":"Section 25","data":[{"key":"Section 29","value":"appartenente"}],"children":[]},{"id":"1695_34","name":"Section 30","data":[{"key":"Section 29","value":"appartenente"}],"children":[]},{"id":"1938_35","name":"Section 31","data":[{"key":"Section 29","value":"appartenente"}],"children":[]},{"id":"5138_36","name":"Section 32","data":[{"key":"Section 29","value":"appartenente"}],"children":[]},{"id":"53549_37","name":"Section 33","data":[{"key":"Section 29","value":"appartenente"}],"children":[]},{"id":"113510_38","name":"Section 34","data":[{"key":"Section 29","value":"appartenente"}],"children":[]},{"id":"113512_39","name":"Section 35","data":[{"key":"Section 29","value":"is person"}],"children":[]}]}],"data":[]};
	Config.drawMainCircle = false;
	var canvas= new Canvas('infovis', '#ddd', '#ddd');
	var ht= new Hypertree(canvas);
    var effectHash = {};
    //Add a controller to assign the node's name to the created label.
	ht.controller = {
	
	onBeforeCompute: function(node) {
  		/*Log.write("centering");*/
  	},
	
  	getName: function(node1, node2) {
  		for(var i=0; i<node1.data.length; i++) {
  			var dataset = node1.data[i];
  			if(dataset.key == node2.name) return dataset.value;
  		}
  		
		for(var i=0; i<node2.data.length; i++) {
  			var dataset = node2.data[i];
  			if(dataset.key == node1.name) return dataset.value;
  		}
  	},
		
  	onCreateLabel: function(domElement, node) {
  		$(domElement).setHTML(node.name).addEvents({

	//Call the "onclick" method from the hypertree to move the hypertree correspondingly.
	//This method takes the native event object. Since Mootools uses a wrapper for this 
	//event, I have to put e.event to get the native event object.
			'click': function(e) {
				ht.onClick(e.event);
			}
  		});
  		
  		var d = $(domElement);
  		effectHash[node.id] = new Fx.Tween(d, 'opacity', {duration:300, transition:Fx.Transitions.linear, wait:false});
  		//d.setOpacity(0.8);
  		d.set('html', node.name).addEvents({
  			'mouseenter': function() {
  				effectHash[node.id].start(0.8, 1);
  			},
  			
  			'mouseleave': function() {
  				effectHash[node.id].start(1, 0.8);
  			}
  		});
  		
  	 },
  	 
  	//Take the left style property and substract half of the label actual width.
  	onPlaceLabel: function(tag, node) {
  		var width = tag.offsetWidth;
  		var intX = tag.style.left.toInt();
  		intX -= width/2;
  		tag.style.left = intX + 'px';
  	},
  	
  	onAfterCompute: function() {
  		/*completata l'animazione, parte il link*/
  		
		//alert("il link!");
  		
  		/*Log.write("done");*/
  		var node = GraphUtil.getClosestNodeToOrigin(ht.graph, "pos");
  		var _self = this;
  		var html = "<h4>" + node.name + "</h4><b>Connections:</b>";
  		html += "<ul>";
 		GraphUtil.eachAdjacency(ht.graph, node, function(child) {
 			if(child.data && child.data.length > 0) {
 				html += "<li>" + child.name + " " + "<div class=\"relation\">(relation: " + _self.getName(node, child) + ")</div></li>";
 			}
 		});
 		html+= "</ul>";
  		$('inner-details').set("html", html);
  	}
  	
   };
  
	ht.loadTreeFromJSON(json);
	ht.compute();
	ht.plot();
	ht.prepareCanvasEvents();
	ht.controller.onAfterCompute();
}