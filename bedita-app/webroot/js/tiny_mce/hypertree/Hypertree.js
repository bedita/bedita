/*
 * File: Hypertree.js
 * 
 * Author: Nicolas Garcia Belmonte
 * 
 * Copyright: Copyright 2008 by Nicolas Garcia Belmonte.
 * 
 * License: BSD License
 * 
 * * Copyright (c) 2008, Nicolas Garcia Belmonte
 * All rights reserved.
 
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the organization nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY Nicolas Garcia Belmonte ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Nicolas Garcia Belmonte BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * Homepage: <http://thejit.org>
 * 
 * Version: 1.0a
 */

/*
   Class: Canvas

   A multi-purpose Canvas object decorator.
*/

/*
   Constructor: Canvas

   Canvas initializer.

   Parameters:

      canvasId - The canvas tag id.
      fillStyle - (optional) fill color style. Default's to black
      strokeStyle - (optional) stroke color style. Default's to black

   Returns:

      A new Canvas instance.
*/
var Canvas= function (canvasId, fillStyle, strokeStyle) {
	//browser supports canvas element
		this.canvasId= canvasId;
		//canvas element exists
		if((this.canvas= document.getElementById(this.canvasId)) 
			&& this.canvas.getContext) {
		      this.ctx = this.canvas.getContext('2d');
		      this.ctx.fillStyle = '#ff7c00';
		      this.ctx.strokeStyle = 'white';
		      this.setPosition();
		      this.translateToCenter();
		} else {
			throw "Canvas object could not initialize.";
		}
	
};

Canvas.prototype= {
	/*
	   Method: getContext

	   Returns:
	
	      Canvas context handler.
	*/
	getContext: function () {
		return this.ctx;
	},

	/*
	   Method: setPosition
	
	   Calculates canvas absolute position on HTML document.
	*/	
	setPosition: function() {
		var obj= this.canvas;
		var curleft = curtop = 0;
		if (obj.offsetParent) {
			curleft = obj.offsetLeft
			curtop = obj.offsetTop
			while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft
				curtop += obj.offsetTop
			}
		}
		this.position= { x: curleft, y: curtop };
	},

	/*
	   Method: getPosition

	   Returns:
	
	      Canvas absolute position to the HTML document.
	*/
	getPosition: function() {
		return this.position;
	},

	/*
	   Method: clear
	
	   Clears the canvas object.
	*/		
	clear: function () {
		this.ctx.clearRect(-this.getSize().x / 2, -this.getSize().x / 2, this.getSize().x, this.getSize().x);
	},

	/*
	   Method: drawMainCircle
	
	   Draws the boundary circle for the Hyperbolic Tree.
	*/	
	drawMainCircle: function () {	
	  var ctx= this.ctx;
	  ctx.beginPath();
  	ctx.arc(0, 0, this.getSmallerSize() / 1, 0, Math.PI*0, true);
  	ctx.stroke();
 		ctx.closePath();
	},
	
	/*
	   Method: translateToCenter
	
	   Translates canvas coordinates system to the center of the canvas object.
	*/
	translateToCenter: function() {
		/*this.ctx.translate(this.canvas.width / 2, this.canvas.height / 2);*/
		this.ctx.translate(255, 255);
	},
	

	/*
	   Method: getSize

	   Returns:
	
	      An object that contains the canvas width and height.
	      i.e. { x: canvasWidth, y: canvasHeight }
	*/
	getSize: function () {
		/*var width = this.canvas.width;
		var height = this.canvas.height;*/
		return { x: 500, y: 500 };
	},

	/*
	   Method: path
	   
	  Performs a _beginPath_ executes _action_ doing then a _type_ ('fill' or 'stroke') and closing the path with closePath.
	*/
	path: function(type, action) {
		this.ctx.beginPath();
		action(this.ctx);
		this.ctx[type]();
		this.ctx.closePath();
	},
	
	/*
	   Method: getSmallerSize
	   
	  Returns min(width, height) for the canvas object.
	*/
	getSmallerSize: function() {
		var s = this.getSize();
		return (s.x <= s.y)? s.x : s.y;
	}
	
};


/*
   Class: Complex
	
	 A multi-purpose Complex Class with common methods.

*/


/*
   Constructor: Complex

   Complex constructor.

   Parameters:

      re - A real number.
      im - An real number representing the imaginary part.


   Returns:

      A new Complex instance.
*/
var Complex= function() {
	if (arguments.length > 1) {
		this.x= arguments[0];
		this.y= arguments[1];
		
	} else {
		this.x= null;
		this.y= null;
	}
	
}

Complex.prototype= {

	/*
	   Method: toPolar
	
	   Transforms cartesian to polar coordinates.
	
	   Returns:
	
	      A new <Polar> instance.
	*/
	toPolar: function() {
		var rho = this.norm();
		var atan = Math.atan2(this.y, this.x);
		if(atan < 0) atan += Math.PI * 2;
		return new Polar(atan, rho);
	},
	/*
	   Method: norm
	
	   Calculates the complex norm.
	
	   Returns:
	
	      A real number representing the complex norm.
	*/
	norm: function () {
		return Math.sqrt(this.squaredNorm());
	},
	
	/*
	   Method: squaredNorm
	
	   Calculates the complex squared norm.
	
	   Returns:
	
	      A real number representing the complex squared norm.
	*/
	squaredNorm: function () {
		return this.x*this.x + this.y*this.y;
	},

	/*
	   Method: add
	
	   Returns the result of adding two complex numbers.
	   Does not alter the original object.

	   Parameters:
	
	      pos - A Complex initialized instance.
	
	   Returns:
	
	     The result of adding two complex numbers.
	*/
	add: function(pos) {
		return new Complex(this.x + pos.x, this.y + pos.y);
	},

	/*
	   Method: prod
	
	   Returns the result of multiplying two complex numbers.
	   Does not alter the original object.

	   Parameters:
	
	      pos - A Complex initialized instance.
	
	   Returns:
	
	     The result of multiplying two complex numbers.
	*/
	prod: function(pos) {
		return new Complex(this.x*pos.x - this.y*pos.y, this.y*pos.x + this.x*pos.y);
	},

	/*
	   Method: conjugate
	
	   Returns the conjugate por this complex.

	   Returns:
	
	     The conjugate por this complex.
	*/
	conjugate: function() {
		return new Complex(this.x, -this.y);
	},


	/*
	   Method: scale
	
	   Returns the result of scaling a Complex instance.
	   Does not alter the original object.

	   Parameters:
	
	      factor - A scale factor.
	
	   Returns:
	
	     The result of scaling this complex to a factor.
	*/
	scale: function(factor) {
		return new Complex(this.x * factor, this.y * factor);
	},

	/*
	   Method: moebiusTransformation
	
	   Calculates a moebius transformation for this point / complex.
	   	For more information go to:
			http://en.wikipedia.org/wiki/Moebius_transformation.

	   Parameters:
	
	      theta - A real number representing a rotation angle.
	      c - An initialized Complex instance representing a translation Vector.
	*/
	moebiusTransformation: function(theta, c) {
		var num= this.add(c.scale(-1));
		var den= new Complex(1, 0).add(c.conjugate().prod(this).scale(-1));
		var numProd= den.conjugate();
		var denProd= den.prod(den.conjugate()).x;
		num= num.prod(numProd).scale(1 / denProd);
		return new Complex(num.x, num.y);
	}
};


/*
   Class: Polar

   A multi purpose polar representation.

*/

/*
   Constructor: Polar

   Polar constructor.

   Parameters:

      theta - An angle.
      rho - The norm.


   Returns:

      A new Polar instance.
*/
var Polar = function(theta, rho) {
	this.theta = theta;
	this.rho = rho;
};

Polar.prototype = {
	/*
	   Method: toComplex
	
	    Translates from polar to cartesian coordinates and returns a new <Complex> instance.
	
	   Returns:
	
	      A new Complex instance.
	*/
	toComplex: function() {
		return new Complex(Math.cos(this.theta), Math.sin(this.theta)).scale(this.rho);
	},

	/*
	   Method: add
	
	    Adds two <Polar> instances.
	
	   Returns:
	
	      A new Polar instance.
	*/
	add: function(polar) {
		return new Polar(this.theta + polar.theta, this.rho + polar.rho);
	},
	
	/*
	   Method: scale
	
	    Scales a polar norm.
	
	   Returns:
	
	      A new Polar instance.
	*/
	scale: function(number) {
		return new Polar(this.theta, this.rho * number);
	}
};



/*
   Object: Config

   <HT> global configuration object. Contains important properties to enable customization and proper behavior for the <HT>.
*/

var Config= {
		//Property: labelContainer
		//id for label container
		labelContainer: 'label_container',
		
		//Property: drawMainCircle
		//show/hide main circle
		drawMainCircle: true,
        
		//Property: levelDistance
		//The actual distance between levels (belongs to (0, 1)).
		levelDistance: 0.6,
		
		//Property: angleRate
		//Change this value to increase or decrease the angle width
		angleRate: 0.6,

		//Property: limit
		//Sometimes putting a big angle rate generates undesirable plots. This way you can set a max angle to apply when the angle span generated exceeds it.
		limit: Math.PI,
		
		//Property: fps
		//animation frames per second
		fps:20,

		//Property: animationTime
		//Time of the animation
		animationTime: 1500,

		//Property: nodeRadius
		//The radius of the nodes displayed
		nodeRadius: 4
};


/*
   Object: GraphUtil

   A multi purpose object to do graph traversal and processing.
*/
var GraphUtil = {

	/*
	   Method: getClosestNodeToOrigin
	
	   Returns the closest node to the center of canvas.
	*/
	getClosestNodeToOrigin: function(graph, prop) {
		var node = null;
		this.eachNode(graph, function(elem) {
			node = (node == null || elem[prop].rho < node[prop].rho)? elem : node;
		});
		return node;
	},
	
	/*
	   Method: eachAdjacency
	
	   Iterates over a _node_ adjacencies applying the _action_ function.
	*/
	eachAdjacency: function(graph, node, action) {
		for(var i=0, adjs = node.adjacencies; i<adjs.length; i++) action(this.getNode(graph, adjs[i]));
	},
	
	/*
	   Method: eachNode
	
	   Iterates over graph nodes performing an action.
	*/
	eachNode: function(graph, action) {
		for(var i in graph.nodes) action(graph.nodes[i]);
	},
	
	/*
	   Method: getNode
	
	   Returns a node from a specified id.
	*/
	getNode: function(graph, id) {
		return graph.nodes[id];
	},
	
	/*
	   Method: eachBFS
	
	   Performs a BFS traversal of a graph beginning by the node of id _id_ and performing _action_ on each node.
	*/
	eachBFS: function(graph, id, action) {
		var _self = this;
		this.eachNode(graph, function(elem) { elem._flag = false; });
		var queue = [this.getNode(graph, id)];
		while(queue.length != 0) {
			var node = queue.pop();
			node._flag = true;
			action(node, node._depth);
			for(var i=0, adj = node.adjacencies; i<adj.length; i++) {
				var n = this.getNode(graph, adj[i]);
				if(n._flag == false) {
					n._depth = node._depth + 1;
					queue.unshift(n);
				}
			}
		}
	},
	
	/*
	   Method: eachSubnode
	
	   After a BFS traversal the _depth_ property of each node has been modified. Now the graph can be traversed as a tree. This method iterates for each subnode that has depth larger than the specified node.
	*/
	eachSubnode: function(graph, node, action) {
		var d = node._depth;
		for(var i=0, ad = node.adjacencies; i<ad.length; i++) {
			var n = this.getNode(graph, ad[i]);
			if(n._depth > d) action(n);
		}
	},
	
	/*
	   Method: getParents
	
	   Returns all nodes having a depth that is less than the node's depth property.
	*/
	getParents: function(graph, node) {
		var adj = node.adjacencies;
		var ans = new Array();
		for(var i=0; i<adj.length; i++) {
			var n = this.getNode(graph, adj[i]);
			if(n._depth < node._depth) ans.push(n);
		}
		return ans;
	},
	
	/*
	 Method: moebiusTransformation
	
	Calculates a moebius transformation for the hyperbolic tree.
	For more information go to:
	<http://en.wikipedia.org/wiki/Moebius_transformation>
	 
	 Parameters:
	
	    theta - Rotation angle.
	    c - Translation Complex.
	*/	
	moebiusTransformation: function(graph, theta, pos, prop, startPos) {
		var propArray = prop, posArray  = pos;
  		this.eachNode(graph, function(elem) {
  			for(var i=0; i<propArray.length; i++)
  				if(startPos) 
  					elem[propArray[i]] = elem[startPos].toComplex().moebiusTransformation(theta, pos[i]).toPolar();
  				else
	  				elem[propArray[i]] = elem[propArray[i]].toComplex().moebiusTransformation(theta, pos[i]).toPolar();
  		});
    }
	
};

/*
   Object: GraphPlot

   An object that performs specific radial layouts for a generic graph structure.
*/
var GraphPlot = {
	//Property: labelsHidden
	//A flag value indicating if node labels are being displayed or not.
	labelsHidden: false,
	//Property: labels
	//Contains an Array of labels plotted.
	labels: {},
	busy: false,
	
	/*
	   Method: hideLabels
	
	   Hides all labels.
	*/
	hideLabels: function (graph, hide, canvas, controller) {
		if(hide)
			for(var labelId in this.labels) { 
				graph.nodes[labelId]._centered = false;
				this.labels[labelId].style.display = 'none'; 
			}
		//only show centered node and its adjacencies.
		else {
			var _self = this;
			var centeredNode = GraphUtil.getClosestNodeToOrigin(graph, 'pos');
			centeredNode._centered = true;
			this.plotLabel(canvas, centeredNode, controller);
			GraphUtil.eachAdjacency(graph, centeredNode, function(elem) {
				_self.plotLabel(canvas, elem, controller);
			});
		}
		this.labelsHidden = hide;
	},
	
	/*
	   Method: clearLabels
	
	   Clears the label container.
	*/
	clearLabels: function() {
		var container = document.getElementById(Config.labelContainer);
		container.innerHTML = '';
		this.labels = {};
	},
	
	/*
	   Method: animate
	
	   Animates the graph by performing a moebius transformation from the current state to the given position.
	*/
	animate: function(graph, id, canvas, directionVector, controller) {
		var _self = this;
		this.hideLabels(graph, true);
		var root = GraphUtil.getNode(graph, id);
		var interpolate = function(delta) {
			var pos = directionVector.scale(delta);
			GraphUtil.moebiusTransformation(graph, new Complex(1, 0), [pos], ['pos'], 'startPos');
		};
		var animationController = {
			compute: function(delta) {
				canvas.clear();
				if(Config.drawMainCircle) canvas.drawMainCircle();
				interpolate(delta);
				GraphPlot.plot(graph, id, canvas);
			},
			
			complete: function() {
				GraphUtil.moebiusTransformation(graph, new Complex(1, 0), [directionVector], ['pos'], 'startPos');
				GraphUtil.eachNode(graph, function(elem) {elem.startPos = new Polar(elem.pos.theta, elem.pos.rho);});
				_self.hideLabels(graph, false, canvas, controller);
				_self.plot(graph, id, canvas);
				_self.busy = false;
				if(controller && controller.onAfterCompute) controller.onAfterCompute();
			}		
		};
		var _self = this;
		Animation.controller = animationController;
		Animation.start();
	},
	
	/*
	   Method: plot
	
	   Plots a Graph.
	*/
	plot: function(graph, id, canvas) {
		var aGraph = graph;
		var _self = this;
		canvas.clear();
		if(Config.drawMainCircle) canvas.drawMainCircle();
		GraphUtil.eachBFS(graph, id, function(elem, i) {
			_self.plotNode(elem, canvas);
			GraphUtil.eachSubnode(aGraph, elem, function(child) {
				_self.plotLine(elem, child, canvas);
			});
		});
	},

	/*
	   Method: plotNode
	
	   Plots a graph node.
	*/
	plotNode: function(node, canvas) {
		var scale = canvas.getSmallerSize() / 2;
		var pos = node.pos.toComplex().scale(scale);
		canvas.path('fill', function(context) {
	  		context.arc(pos.x, pos.y, Config.nodeRadius, 0, Math.PI*2, true);			
		});
	},
	
	/*
	   Method: plotLine
	
	   Plots a hyperline between to nodes. A hyperline is an arc of a circle which is orthogonal to the main circle.
	*/
	plotLine: function(node, child, canvas) {
		var correctAngle = function(angle) { return (angle > 0)? angle : angle + Math.PI * 2; };
		var pos = node.pos.toComplex(), posChild = child.pos.toComplex();
		var centerOfCircle = this.computeArcThroughTwoPoints(pos, posChild);
		var scale = canvas.getSmallerSize()/2;
		
		var angleBegin = correctAngle(Math.atan2(posChild.y - centerOfCircle.y, posChild.x - centerOfCircle.x));
   		var angleEnd   = correctAngle(Math.atan2(pos.y - centerOfCircle.y, pos.x - centerOfCircle.x));
		var sense      = this.sense(angleBegin, angleEnd);
		
		var context = canvas.getContext();
		context.save();
		canvas.path('stroke', function(ctx) {
		 	if(centerOfCircle.a > 1000 || centerOfCircle.b > 1000 || centerOfCircle.ratio > 1000) {
				var posScaled = pos.scale(scale), posChildScaled = posChild.scale(scale); 
		  		ctx.moveTo(posScaled.x, posScaled.y);
		  		ctx.lineTo(posChildScaled.x, posChildScaled.y);
		  	} else {
		    		ctx.arc(centerOfCircle.x*scale, centerOfCircle.y*scale, centerOfCircle.ratio*scale, angleBegin, angleEnd, sense);
		  	}
		});
		context.restore();
	},
	
	/*
	   Method: plotLabel
	
	   Plots a label for a given node.
	*/
	plotLabel: function(canvas, node, controller) {
		var id = node.id;
		var tag = false;
		if(!(tag = this.labels[id])) {
			tag = document.createElement('div');
			var container = document.getElementById(Config.labelContainer);
			container.appendChild(tag);
			if(controller && controller.onCreateLabel) controller.onCreateLabel(tag, node);
		}
		var pos = node.pos.toComplex();
		var radius= canvas.getSize();
		var scale = canvas.getSmallerSize() / 2;
		var canvasPos = canvas.getPosition();
		var labelPos= {
			x: Math.round(pos.x * scale + canvasPos.x + radius.x/2),
			y: Math.round(pos.y * scale + canvasPos.y + radius.y/2)
		};
		tag.id = id;
		tag.className = 'node';
		tag.style.position = 'absolute';
		tag.style.left = labelPos.x + 'px';
		tag.style.top = labelPos.y  + 'px';
		tag.style.display = '';
		this.labels[id] = tag;
		if(controller && controller.onPlaceLabel) controller.onPlaceLabel(tag, node);
	},
	
	/*
	   Method: computeArcThroughTwoPoints
	
	   Calculates the arc parameters through two points. More information in <http://en.wikipedia.org/wiki/Poincar%C3%A9_disc_model#Analytic_geometry_constructions_in_the_hyperbolic_plane>
	*/
	computeArcThroughTwoPoints: function(p1, p2) {
	  	var aDen = (p1.x*p2.y - p1.y*p2.x);
	  	var bDen = (p1.x*p2.y - p1.y*p2.x);
	  	//Setting ratio to > 1000 to draw a straight line.
	  	if (aDen == 0 || bDen == 0 ) return { x:0, y:0, ratio: 1001 };

	  	var a = (p1.y*(p2.squaredNorm()) - p2.y * (p1.squaredNorm()) + p1.y - p2.y) / aDen;
	  	var b = (p2.x*(p1.squaredNorm()) - p1.x * (p2.squaredNorm()) + p2.x - p1.x) / bDen;
	  	var x = -a / 2;
	  	var y = -b / 2;
	  	var ratio = Math.sqrt((a*a + b*b) / 4 -1);
	  	
	  	var out= {
	  		x: x,
	  		y: y,
	  		ratio: ratio,
	  		a: a,
	  		b: b
	  	};
	
	  	return out;
  },
  
  
	/*
	   Method: sense

	   For private use only: sets angle direction to clockwise (true) or counterclockwise (false).
	   
	   Parameters:
	
	      angleBegin - Starting angle for drawing the arc.
	      angleEnd - The HyperLine will be drawn from angleBegin to angleEnd.

	   Returns:
	
	      A Boolean instance describing the sense for drawing the HyperLine.
	*/
  sense: function(angleBegin, angleEnd) {
  	return (angleBegin < angleEnd)? ((angleBegin + Math.PI > angleEnd)? false : true) : ((angleEnd + Math.PI > angleBegin)? true : false);
  }
  
	
			
};

/*
   Class: Hypertree

	An animated Graph with radial layout.

	Go to <http://blog.thejit.org/?p=7> to know what kind of JSON structure feeds this object.
	
	Go to <http://blog.thejit.org/?p=8> to know what kind of controller this class accepts.
	
	Refer to the <Config> object to know what properties can be modified in order to customize this object. 

	The simplest way to create and layout a Hypertree is:
	
	(start code)

	  var canvas= new Canvas('infovis', '#fff', '#fff');
	  var ht= new Hypertree(canvas);
	  ht.loadTreeFromJSON(json);
	  ht.compute();
	  ht.plot();
	  ht.prepareCanvasEvents();

	(end code)

	A user should only interact with the Canvas, Hypertree and Config objects/classes.
	By implementing Hypertree controllers you can also customize the Hypertree behavior.
*/

/*
 Constructor: Hypertree

 Creates a new Hypertree instance.
 
 Parameters:

    canvas - A <Canvas> instance.
    controller - _optional_ a Hypertree controller <http://blog.thejit.org/?p=8>
*/
var Hypertree = function(canvas, controller) {
	this.controller = controller || false;
	this.graph = new Graph();
	this.json = null;
	this.canvas = canvas;
	this.root = null;
	this.theta= new Complex(1, 0);
	
};

Hypertree.prototype = {

	/*
	 Method: loadTree
	
	 Loads a Graph from a json tree object <http://blog.thejit.org/?p=7>
	 
	*/
	loadTree: function(json) {
		var ch = json.children;
		for(var i=0; i<ch.length; i++) {
			this.graph.addAdjacence(json, ch[i]);
			this.loadTree(ch[i]);
		}
	},
	
	/*
	 Method: flagRoot
	
	 Flags a node specified by _id_ as root.
	*/
	flagRoot: function(id) {
		this.unflagRoot();
		this.graph.nodes[id]._root = true;
	},
	
	/*
	 Method: unflagRoot
	
	 Unflags all nodes.
	*/
	unflagRoot: function() {
		GraphUtil.eachNode(this.graph, function(elem) {elem._root = false;});
	},
	
	/*
	 Method: getRoot
	
	 Returns the node flagged as root.
	*/
	getRoot: function() {
		var root = false;
		GraphUtil.eachNode(this.graph, function(elem){ if(elem._root) root = elem; });
		return root;
	},
	
	/*
	 Method: loadTreeFromJSON
	
	 Loads a Hypertree from a _json_ object <http://blog.thejit.org/?p=7>
	*/
	loadTreeFromJSON: function(json) {
		this.json = json;
		this.loadTree(json);
		this.root = json.id;
	},
	
	/*
	 Method: plot
	
	 Plots the Hypertree
	*/
	plot: function() {
		GraphPlot.plot(this.graph, this.root, this.canvas, this);
		GraphPlot.hideLabels(this.graph, false, this.canvas, this.controller);
	},
	
	/*
	 Method: compute
	
	 Computes the graph nodes positions and stores this positions on _property_.
	*/
	compute: function(property) {
		var prop = property || ['pos', 'startPos'];
		var node = GraphUtil.getNode(this.graph, this.root);
		node._depth = 0;
		this.flagRoot(this.root);
		GraphUtil.eachBFS(this.graph, this.root, function(elem, i) { elem.pos = new Polar(0, 0); elem.startPos = new Polar(0, 0); });
		this.computeAngularWidths();
		this.computePositions(prop);
	},
	
	/*
	 Method: computePositions
	
	 Performs the main algorithm for computing node positions.
	*/
	computePositions: function(property) {
		var propArray = (typeof property == 'array' || typeof property == 'object')? property : [property];
		var aGraph = this.graph;
		var root = GraphUtil.getNode(this.graph, this.root);
		var _self = this;
		for(var i=0; i<propArray.length; i++) root[propArray[i]] = new Polar(0, 0);
		root.angleSpan = {
			begin: 0,
			end: 2 * Math.PI
		};
		GraphUtil.eachBFS(this.graph, this.root, function (elem) {
			if(!elem._root) {
				for(var i=0, pos = []; i<propArray.length; i++) 
					pos.push(elem[propArray[i]].toComplex());
				GraphUtil.moebiusTransformation(_self.graph, _self.theta, pos, propArray);	
			}
			var angleSpan = elem.angleSpan.end - elem.angleSpan.begin;
			var angleInit = elem.angleSpan.begin;
			var totalAngularWidths = (function (element){
				var total = 0;
				GraphUtil.eachSubnode(aGraph, element, function(sib) {
					total += sib._treeAngularWidth;
				});
				return total;
			})(elem);
			var rho = Config.levelDistance;
			GraphUtil.eachSubnode(aGraph, elem, function(child) {
				var angleProportion = child._treeAngularWidth / totalAngularWidths * angleSpan;
				var theta = angleInit + angleProportion / 2;
				for(var i=0; i<propArray.length; i++)
					child[propArray[i]] = new Polar(theta, rho);
				
				var span = angleProportion + (2 * Config.angleRate * angleProportion);
				var rate = (span >= Config.limit)? 0 : Config.angleRate;
				
				child.angleSpan = {
					begin: angleInit - rate * angleProportion,
					end: angleInit + angleProportion + (rate * angleProportion)
				};
				angleInit += angleProportion;
			});
			if(!elem._root) {
				for(var i=0; i<propArray.length; i++) 
					pos[i] = pos[i].scale(-1);
				GraphUtil.moebiusTransformation(_self.graph, _self.theta, pos, propArray);	
			}
		});
	},
	
	/*
	 Method: setSubtreesAngularWidths
	
	 Sets subtrees angular widths.
	*/
	setSubtreesAngularWidth: function() {
		var _self = this;
		GraphUtil.eachNode(this.graph, function(elem) {
			_self.setSubtreeAngularWidth(elem);
		});
	},
	
	/*
	 Method: setSubtreeAngularWidth
	
	 Sets the angular width for a subtree.
	*/
	setSubtreeAngularWidth: function(elem) {
		var _self = this, nodeAW = 1, sumAW = 0;
		GraphUtil.eachSubnode(this.graph, elem, function(child) {
			_self.setSubtreeAngularWidth(child);
			sumAW++;
		});
		elem._treeAngularWidth = Math.max(nodeAW, sumAW);
	},
	
	/*
	 Method: computeAngularWidths
	
	 Computes nodes and subtrees angular widths.
	*/
	computeAngularWidths: function () {
		this.setSubtreesAngularWidth();
	},
	
	/*
	 Method: onClick
	
	 Performs all calculations and animation when clicking on a label specified by _id_. The label id is the same id as its homologue node.
	*/
	onClick: function(e) {
		Mouse.capturePosition(e);
		var mousePosition = Mouse.getPosition(this.canvas);
		if(GraphPlot.busy === false && mousePosition.norm() < 1) {
			GraphPlot.busy = true;
			var root = GraphUtil.getNode(this.graph, this.root);
			if(this.controller && this.controller.onBeforeCompute) this.controller.onBeforeCompute(root);
			var directionVector = new Complex(mousePosition.x, mousePosition.y);
			if(directionVector.norm() < 1)
				GraphPlot.animate(this.graph, this.root, this.canvas, directionVector, this.controller);
		}
	},
	
	/*
	 Method: prepareCanvasEvents
	
	 Adds a click handler to the canvas in order to translate the Hypertree when clicking anywhere on the canvas. You could set a translation handler on the labels and not activate this one if you want to. You can perform that by using a controller <http://blog.thejit.org/?p=8>
	*/
	prepareCanvasEvents: function() {
		var _self = this;
		this.canvas.canvas.onclick = function(e) { _self.onClick(e); };
	}
	
};


/*
   Class: Mouse

   A multi-purpose Mouse class.
*/
var Mouse = {
	  
	  position: null,

		/*
		   Method: getPosition
		
		   Returns mouse position relative to canvas.
		
		   Parameters:
		
		      canvas - A canvas object.
		
		   Returns:
		
		      A Complex instance representing the mouse position on the canvas.
		*/
		getPosition: function (canvas) {
			var posx = this.posx;
			var posy = this.posy;
			var position = canvas.getPosition();
			var s = canvas.getSmallerSize();
			var size = canvas.getSize();
			var coordinates= {
			  x: ((posx - position.x) - size.x / 2) / (s / 2),
			  y: ((posy - position.y) - size.y / 2) / (s / 2)
			};
			
			this.position= new Complex(coordinates.x, coordinates.y);
			return this.position;
		},


		/*
		   Method: capturePosition
		
		   Captures mouse position.
		
		   Parameters:
		
		      e - Triggered event.
		*/
	  capturePosition: function(e) {
			var posx = 0;
			var posy = 0;
			if (!e) var e = window.event;
			if (e.pageX || e.pageY) 	{
				posx = e.pageX;
				posy = e.pageY;
			}
			else if (e.clientX || e.clientY) 	{
				posx = e.clientX + document.body.scrollLeft
					+ document.documentElement.scrollLeft;
				posy = e.clientY + document.body.scrollTop
					+ document.documentElement.scrollTop;
			}
			
			this.posx= posx;
			this.posy= posy;
	}
};




/*
   Class: Graph

   A generic graph class.
*/

/*
 Constructor: Graph

 Creates a new Graph instance.
 
*/	
var Graph= function()  {
	//Property: nodes
	//graph nodes
	this.nodes= {};
};
	
	
Graph.prototype= {
	/*
	 Method: addAdjacence
	
	 Connects nodes specified by *obj* and *obj2*. If not found, nodes are created.
	 
	 Parameters:
	
	    obj - a <Graph.Node> object.
	    obj2 - Another <Graph.Node> object.
	*/	
  addAdjacence: function (obj, obj2) {
  	if(!this.hasNode(obj.id)) this.addNode(obj);
  	if(!this.hasNode(obj2.id)) this.addNode(obj2);
  
  	for(var i in this.nodes) {
  		if(this.nodes[i].id == obj.id) {
  			if(!this.nodes[i].adjacentTo(obj2.id)) {
  				this.nodes[i].addAdjacency(obj2.id);
  			}
  		}
  		
  		if(this.nodes[i].id == obj2.id) {	
  			if(!this.nodes[i].adjacentTo(obj.id)) {
  				this.nodes[i].addAdjacency(obj.id);
  			}
  		}
  	}
 },

	/*
	 Method: addNode
	
	 Adds a node.
	 
	 Parameters:
	
	    obj - A <Graph.Node> object.
	*/	
  addNode: function(obj) {
  	if(!this.nodes[obj.id]) {
	  	var node= new Graph.Node(obj.id, obj.name, obj.data);
	  	this.nodes[obj.id]= node;
  	}
  },


	/*
	 Method: hasNode
	
	 Returns a Boolean instance indicating if node belongs to graph or not.
	 
	 Parameters:
	
	    id - Node id.

	 Returns:
	  
	 		A Boolean instance indicating if node belongs to graph or not.
	*/	
  hasNode: function(id) {
  	for(var index in this.nodes) {
  		if (index== id) {
  			return true;
  		}
  	}
  	return false;	
  }
};
/*
   Class: Graph.Node
	
	 Behaviour of the <Graph> node.

*/
/*
   Constructor: Graph.Node

   Node constructor.

   Parameters:

      id - The node *unique identifier* id.
      name - A node's name.
      data - Place to store some extra information (can be left to null).


   Returns:

      A new <Graph.Node> instance.
*/
Graph.Node = function(id, name, data) {
	//Property: id
	this.id= id;
	//Property: name
	this.name = name;
	//Property: data
	//The dataSet object <http://blog.thejit.org/?p=7>
	this.data = data;
		
	//Property: drawn
	//Node flag
	this.drawn= false;

	//Property: angle span
	//allowed angle span for adjacencies placement
	this.angleSpan= {
		begin:0,
		end:0
	};

	//Property: pos
	//node position
	this.pos= new Polar(0, 0);
	
	//Property: startPos
	//node from position
	this.startPos= new Polar(0, 0);
	
	//Property: endPos
	//node to position
	this.endPos= new Polar(0, 0);
	
	//Property: adjacencies
	//node adjacencies
	this.adjacencies= new Array();
	
}

Graph.Node.prototype= {
	
		/*
	   Method: adjacentTo
	
	   Indicates if the node is adjacent to the node indicated by the specified id

	   Parameters:
	
	      id - A node id.
	
	   Returns:
	
	     A Boolean instance indicating whether this node is adjacent to the specified by id or not.
	*/
	adjacentTo: function(id) {
		for(var index=0; index<this.adjacencies.length; index++) {
			if(id== this.adjacencies[index]) {
				return true;
			}
		}
		return false;
	},

		/*
	   Method: addAdjacency
	
	   Connects the node to the specified by id.

	   Parameters:
	
	      id - A node id.
	*/	
	addAdjacency: function(id) {
		this.adjacencies.push(id);
	}
};

/*
   Object: Trans
	
	 An object containing multiple type of transformations. Based on the mootools library <http://mootools.net>.

*/
var Trans = {
	linear: function(p) { return p;	},
	Quart: function(p) {
		return Math.pow(p, 4);
	},
	easeIn: function(transition, pos){
		return transition(pos);
	},
	easeOut: function(transition, pos){
		return 1 - transition(1 - pos);
	},
	easeInOut: function(transition, pos){
		return (pos <= 0.5) ? transition(2 * pos) / 2 : (2 - transition(2 * (1 - pos))) / 2;
	}
};

/*
   Object: Animation
	
	 An object that performs animations. Based on Fx.Base from Mootools.

*/
var Animation = {

	duration: Config.animationTime,
	fps: Config.fps,
	transition: function(p) {return Trans.easeInOut(Trans.Quart, p);},
	//transition: Trans.linear,
	controller: false,
	
	getTime: function() {
		var ans = (Date.now)? Date.now() : new Date().getTime();
		return ans;
	},
	
	step: function(){
		var time = this.getTime();
		if (time < this.time + this.duration){
			var delta = this.transition((time - this.time) / this.duration);
			this.controller.compute(delta);
		} else {
			this.timer = clearInterval(this.timer);
			this.controller.compute(1);
			this.controller.complete();
		}
	},

	start: function(){
		this.time = 0;
		this.startTimer();
		return this;
	},


	stopTimer: function(){
		if (!this.timer) return false;
		this.time = $time() - this.time;
		this.timer = $clear(this.timer);
		return true;
	},

	startTimer: function(){
		if (this.timer) return false;
		this.time = this.getTime() - this.time;
		this.timer = setInterval((function () { Animation.step(); }), Math.round(1000 / this.fps));
		return true;
	}

	
};

