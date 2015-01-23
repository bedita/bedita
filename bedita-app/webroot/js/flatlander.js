Flatlander = function (options) {
	var obj = this;

	if (window["FlatlanderWorkspaceInstances"]==undefined) window["FlatlanderWorkspaceInstances"] = [];
	this.id = window["FlatlanderWorkspaceInstances"].length;

	this.defaults = {
		el: document.body,
		editable: true,
		callbacks: {
			'starterror': [],
			'error': [],
			'loaded': [],
			'change': [],
			'areacreated': [],
		},
		dev: false,
		logprefix: 'FLATLANDER:',
		cssFilename: 'flatlander.css',
	}

	this.classList = {
		wrapper: 'flatlanderWrapper',
		imgwrapper: 'flatlanderImageWrapper',
		workspace: 'flatlanderWorkspace',
		editor: 'flatlanderEditor',
		editable: 'flatlanderEditable',
		area: 'flatlanderArea'
	}

	this.numberOfElements = 0;

	this.areas = {};

	this.support = {
		isFunction: function(functionToCheck) { var getType = {}; return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]'; },
		toPerc: function(x1,x2) { return 100*x1/x2; },
		lastElementClicked: document.body,
	}

	this.updateOptions = function(opt) {
		var obj = this;
		var options = obj.options || this.defaults;
		obj.options = options;
		obj.userOptions = opt;
		for (var k in opt) {
			if (obj.defaults[k]!=undefined) {
				obj.options[k] = opt[k]
			}
		}
		obj.callbacks = obj.options.callbacks;
	}

	this.bind = function(evtName, callback) {
		var obj = this;
		var clls = obj.callbacks[evtName];
		if (clls) {
			if (obj.support.isFunction(callback)) clls.push(callback);
		}
	}

	this.bindDevCallbacks = function(){
		var obj = this;
		this.bind('starterror', function() {
			console.log(obj.options.logprefix,'starterror',obj.el);
		});
		this.bind('loaded', function() {
			console.log(obj.options.logprefix,'workspace ready');
		});
		this.bind('change', function(area) {
			console.log(obj.options.logprefix,'FlatlanderArea',area.get('id'),'in FlatlanderWorkspace',obj.id+'','has changed');
		});
	}

	this.trigger = function(evt, attrs) {
		var obj = this;
		var clls = obj.callbacks[evt];
		if (clls) {
			for (var i = 0; i<clls.length; i++) {
				var cll = clls[i];
				if (obj.support.isFunction(cll)) cll(attrs);
			}
		}
	}

	this.startbehaviour = function() {
		var obj = this;
		obj.$workspace.disableSelection();

		obj.$workspace.bind('mousedown.betag', function(ev) {
			if (!$(ev.target).is('.'+obj.classList.workspace)) {
				return true;
			}
			if (ev.button!=0) {
				ev.preventDefault();
				ev.stopPropagation();
				return false;
			}
			var oX = ev.pageX;
			var oY = ev.pageY;
			var divAppended = false;
			var target = $(this);

			areaOrigin = true;
			offLeft = target.offset().left;
			offTop = target.offset().top;
			coX = obj.support.toPerc(oX - offLeft, obj.$workspace.width());
			coY = obj.support.toPerc(oY - offTop, obj.$workspace.height());
			var div = $('<div></div>');
			div.data('left',coX);
			div.data('top',coY);
			div.addClass(obj.classList.area);
			div.css({
				left: coX+'%',
				top: coY+'%',
			})
			$(document).bind('mousemove.betag', function(ev) {
				if (areaOrigin) {
					obj.$workspace.append(div);
					areaOrigin = false;
					divAppended = true;
				}
				var pX = ev.pageX;
				var pY = ev.pageY;
				var cX = obj.support.toPerc(pX - offLeft, obj.$workspace.width());
				var cY = obj.support.toPerc(pY - offTop, obj.$workspace.height());
				var bL = Math.min(cX,coX);
				var bT = Math.min(cY,coY);
				var bW = Math.abs(cX-coX);
				var bH = Math.abs(cY-coY);

				bW = Math.min(bW,100-bL);
				bH = Math.min(bH,100-bT);

				div.css({
					width: bW+'%',
					height: bH+'%',
					left: bL+'%',
					top: bT+'%',
				});
			});
			$(document).bind('mouseup.betag', function(ev) {
				var n = obj.numberOfElements+1;
				areaOrigin = false;
				if (divAppended) {
					var areaObj = new FlatlanderArea(div, obj);
					areaObj.set({
						priority: n,
						left: parseFloat(div[0].style.left),
						top: parseFloat(div[0].style.top),
						width: parseFloat(div[0].style.width),
						height: parseFloat(div[0].style.height),
					});
					obj.FlatlanderEditorInstance.appendArea(areaObj);
					obj.trigger('areacreated', areaObj);
					$(this).unbind('mouseup.betag').unbind('mousemove.betag');
				}
			})
		})
	}

	this.start = function() {
		var obj = this;
		var el = obj.options.el;
		//obj.loadCSS();
		if (obj.options.dev) this.bindDevCallbacks();
		if (el) {
			var parent = el.parentNode;
			//wrapper
			var wrapper = document.createElement('div');
			wrapper.setAttribute('class', obj.classList.wrapper + ' ' + (obj.options.editable ? 'flatlanderEditable' : ''));
			parent.insertBefore(wrapper, el);
			//imgwrapper
			var imgwrapper = document.createElement('div');
			imgwrapper.setAttribute('class', obj.classList.imgwrapper);
			parent.insertBefore(imgwrapper, el);
			imgwrapper.appendChild(el);
			//workspace
			var workspace = document.createElement('div');
			workspace.setAttribute('class', obj.classList.workspace);
			imgwrapper.appendChild(workspace);
			//editor
			var editor = document.createElement('div');
			editor.setAttribute('class', obj.classList.editor);

			wrapper.appendChild(imgwrapper);
			wrapper.appendChild(editor);

			obj.wrapper = wrapper;
			obj.workspace = workspace;
			obj.editor = editor;

			obj.$wrapper = $(wrapper);
			obj.$workspace = $(workspace);
			obj.$editor = $(editor);

			this.FlatlanderEditorInstance = new FlatlanderEditor(obj.editor, obj);

			obj.startbehaviour();
		} else {
			obj.trigger('starterror');
		}
	}

	this.sortFromArray = function(matrix) {
		var obj = this;
		for (var k in matrix) {
			obj.areas[matrix[k]].set('priority', k);
		}
	}

	this.toJSON = function() {
		var obj = this;
		var j = [];
		for(var i in obj.areas) {
			j.push(obj.areas[i].attr);
		}
		return j;
	}

	this.loadCSS = function() {
		var style = document.createElement("link");
		style.setAttribute("rel", "stylesheet")
		style.setAttribute("type", "text/css")
		style.setAttribute("href", obj.defaults.cssFilename);
		document.getElementsByTagName("head")[0].appendChild(style);
	}

	this.updateOptions(options);
	this.start();
	window["FlatlanderWorkspaceInstances"].push(this);
	this.trigger('loaded');

	return this;
}

FlatlanderArea = function(el, workspace) {
	var obj = this;

	this.workspace = workspace;

	this.attr = {
		id: 'area_'+workspace.numberOfElements,
		priority: 1,
		title: '',
		body: '',
		link: '',
		number: '',
		style: 'fill',
		behaviour: 'popup',
		background: 'none',
		left: 0,
		top: 0,
		width: 0,
		height: 0,
		hotspotX: 50,
		hotspotY: 50,
		deleted: false,
		direction: 'auto',
	}

	workspace.numberOfElements += 1;

	this.callbacks = {
		'change': [],
		'created': [],
		'deleted': []
	};

	this.hotspotHtml = '<div class="hotspot_point"></div>';
	this.$hotspot = $(this.hotspotHtml);

	this.el = $(el)[0];
	this.$el = $(el);

	this.behaviors = {
		onDragAreaStop: function() {
			var area = obj.$el;
			var left = parseFloat(area.css('left'));
			var top = parseFloat(area.css('top'));
			var width = obj.workspace.$workspace.width();
			var height = obj.workspace.$workspace.height();
			var pLeft = 100*left/width;
			var pTop = 100*top/height;
			obj.set({
				left: pLeft,
				top: pTop,
			});
		},
		onResizeAreaStop: function() {
			var area = obj.$el;
			var width = parseFloat(area.css('width'));
			var height = parseFloat(area.css('height'));
			var cwidth = obj.workspace.$workspace.width();
			var cheight = obj.workspace.$workspace.height();
			var pWidth = 100*width/cwidth;
			var pHeight = 100*height/cheight;
			obj.set({
				width: pWidth,
				height: pHeight,
			});
		},
		onDblClickArea: function() {
			obj.workspace.FlatlanderEditorInstance.open();
			obj.onLoad();
		},
		onMousedown: function() {
			obj.workspace.FlatlanderEditorInstance.emptyForms();
			obj.onLoad();
		},
		onDragHotspotStop: function(hs) {
			var hs = obj.$hotspot;
			var left = parseFloat(hs.css('left'));
			var top = parseFloat(hs.css('top'));
			var width = hs.parent().width();
			var height = hs.parent().height();
			var pLeft = 100*left/width;
			var pTop = 100*top/height;
			obj.set({
				hotspotX: pLeft,
				hotspotY: pTop,
			});
		}
	}

	this.set = function(k, v, preventUpload) {
		var obj = this;
		if (k && typeof k == 'string') {
			obj.attr[k] = v;
		} else if (k && typeof k == 'object') {
 			for (var j in k) {
				obj.attr[j] = k[j];
			}
		}

		this.$el.attr('id', obj.get('id'));
		this.$el.attr('data-title', obj.get('title')+' - '+obj.get('link')+' - '+obj.get('id'));

		var bkg = obj.get('background');
		this.$el.css({
			left: obj.get('left')+'%',
			top: obj.get('top')+'%',
			width: obj.get('width')+'%',
			height: obj.get('height')+'%',
			'z-index': 100 - parseInt(obj.get('priority')+''),
			'background-image': (bkg=='none' || obj.get('behaviour')!='skin') ? 'none' : 'url('+bkg+')'
		})

		this.$hotspot.css({
			left: obj.get('hotspotX')+'%',
			top: obj.get('hotspotY')+'%',
		});

		this.onLoad();
		if (preventUpload) return;

		obj.trigger('change');
		obj.workspace.trigger('change', this);
	}

	this.get = function(k) {
		return this.attr[k];
	}

	this.onLoad = function() {
		this.$el.parent().children().removeClass('active');
		this.$el.addClass('active');
		obj.workspace.FlatlanderEditorInstance.fillForms( this );
	}

	this.delete = function() {
		if (this.get('deleted') == true) return;
		this.set('deleted', true);
		this.trigger('deleted', this);
		this.$el.remove();
		delete obj.workspace.areas[this.id];
		obj.workspace.FlatlanderEditorInstance.removeArea(obj);
	}

	this.addFocusPoint = function() {
		var obj = this;
		this.$hotspot.css({
			left: '50%',
			top: '50%'
		})
		this.$el.append(this.$hotspot);
		this.$hotspot.draggable({ containment: 'parent', stop: function() { obj.behaviors.onDragHotspotStop() } });
	}

	this.removeFocusPoint = function() {
		this.$hotspot.remove();
		this.$hotspot = $(this.hotspotHtml);
		this.set({
			hotspotX: null,
			hotspotY: null
		})
	}

	this.hasFocusPoint = function() {
		return this.attr.hotspotX!=null;
	}

	this.addBackground = function(base64Image) {
		this.set('background', base64Image);
	}

	this.removeBackground = function() {
		this.set('background', 'none');
	}

	this.hasBackground = function(){
		return this.get('background')!='none';
	}

	this.bind = function(evtName, callback) {
		var obj = this;
		var clls = obj.callbacks[evtName];
		if (clls) {
			if (obj.workspace.support.isFunction(callback)) clls.push(callback);
		}
	}

	this.trigger = function(evt, attrs) {
		var obj = this;
		var clls = obj.callbacks[evt];
		if (clls) {
			for (var i = 0; i<clls.length; i++) {
				var cll = clls[i];
				if (obj.workspace.support.isFunction(cll)) cll(attrs);
			}
		}
	}

	this.toJSON = function() {
		return this.attr;
	}

	this.$el.draggable({ 
		containment: '.'+obj.workspace.classList.workspace, 
		stop: function() {
			obj.behaviors.onDragAreaStop() 
		}
	}).resizable({ 
		containment: '.'+obj.workspace.classList.workspace,
		stop: function() {
			obj.behaviors.onResizeAreaStop() 
		}
	}).bind('dblclick', function() {
		obj.behaviors.onDblClickArea();
	}).bind('mousedown', function(ev) {
		obj.behaviors.onMousedown();
	});

	this.addFocusPoint();
	this.onLoad();
	this.trigger('created');
	this.trigger('change');
	workspace.areas[this.get('id')] = this;
}

FlatlanderEditor = function(el, workspace) {
	var obj = this;

	this.el = $(el)[0];
	this.$el = $(el);

	this.workspace = workspace;

	this.isOpen = false;

	this.currentArea = null;

	this.altPressed = false;

	this.inputs = null;
	this.inputsCallbacks = {};

	var html = '';
	html += '<div class="fl-editorContainer">';
	html += '<ul class="fl-layers"></ul><button rel="deleteArea">Delete Area</button><hr />';
	html += '</div>';

	this.el.innerHTML = html;

	this.editableInEditor = ['top', 'left', 'width', 'height', 'hotspotX', 'hotspotY'];

	this.toggle = function() {
		if (this.isOpen) this.close()
		else this.open();
	}

	this.open = function() {
		this.isOpen = true;
		this.$el.addClass('open');
	}

	this.close = function() {
		this.isOpen = false;
		this.$el.removeClass('open');
	}

	this.appendArea = function(area) {
		this.currentArea = area;
		var newLi = $('<li data-id="'+area.get('id')+'" data-z="'+area.get('priority')+'">'+area.get('title')+'</li>');
		this.$el.find('.fl-layers').append(newLi);
		newLi.bind('click', function() {
			var id = $(this).attr('data-id');
			obj.workspace.areas[id].set();
		})
		this.fillForms( this.currentArea );
	}

	this.removeArea = function(area) {
		this.$el.find('.fl-layers li[data-id="'+area.get('id')+'"]').remove();
		if (this.$el.find('.fl-layers li').length>0) this.$el.find('.fl-layers li').first().click();
		else this.close();
	}

	this.sort = function() {
		var reorder = {};
		var length = this.$el.find('.fl-layers li').length;
		this.$el.find('.fl-layers li').each(function(index){
			reorder[index+1] = $(this).attr('data-id');
		})
		obj.workspace.sortFromArray(reorder);
	}

	this.emptyForms = function() {
		
	}

	this.fillForms = function(area) {
		var obj = this;
		var eC = this.$el;
		var $area = area.$el;

		if (this.currentArea && area.get('id')!=this.currentArea.get('id')) {
			eC.find('.fl-editorContainer').scrollTop(0);
		}

		this.currentArea = area;

		var attrs = area.attr;

		var list = eC.find('.fl-layers li');
		list.removeClass('fl-active');
		list.filter('[data-id="'+attrs.id+'"]').addClass('fl-active');
		list.each(function() {
			var $t = $(this);
			var id = $t.attr('data-id');
			var a = obj.workspace.areas[id];
			if (a) {
				var title = a.get('title')+' - '+a.get('link')+' - '+a.get('id');
				if (title.length>50) {
					title = title.substring(0,45) + '...';
				}
				$t.text(title);
			}
		})
		
		var listparent = list.parent();
		var order = [];
		for (var k in obj.workspace.areas) {
			var a = obj.workspace.areas[k];
			order[parseInt(a.get('priority'))] = a;
		}
		for (var i=1; i<=order.length; i++) {
			var a = order[i];
			if (a) {
				var el = list.filter('[data-id="'+a.get('id')+'"]');
				if (length>0) listparent.append( el );
			}
		}

		for (var k in attrs) {
			try {
				eC.find('[data-name="'+k+'"]').val( attrs[k] );
			} catch(e){
				if (obj.workspace.options.dev) console.log('cannot set property',k);
			}
		}
	}

	this.bindEvents = function() {
		var obj = this;

		this.$el.find('[rel="deleteArea"]').bind('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			if (confirm('Do you want remove this area ('+obj.currentArea.get('id')+')?')) {
				obj.currentArea.delete();				
			}
			return false;
		});

		$(window).bind('keydown', function(ev) {
			var bind = [37,38,39,40,46,107,109];
			if (ev.keyCode == 18) obj.altPressed = true;
			//up = 38, down = 40 , right = 39, left = 37, canc = 46, plus = 107, minus = 109
			if ($(ev.target).is('body') && obj.currentArea!=null && bind.indexOf(ev.keyCode)>-1) {
				ev.stopPropagation();
				ev.preventDefault();
				switch(ev.keyCode) {
					case 37:
					case 39:
						var add = 1;
						if (ev.keyCode==37) {
							add = -1;
						}
						if (obj.altPressed && obj.currentArea.get('hotspotX')!=null) {
							var v = parseFloat(obj.currentArea.$hotspot.css('left'));
							var p = v*obj.currentArea.$el.width()/100 + add;
							p = p*100/obj.currentArea.$el.width();
							p = Math.max(0, p);
							p = Math.min(100, p);
							obj.currentArea.set('hotspotX',p);
						} else {
							var v = parseFloat(obj.currentArea.$el.css('left'));
							var p = v*obj.workspace.$workspace.width()/100 + add;
							p = p*100/obj.workspace.$workspace.width();
							p = Math.max(0, p);
							p = Math.min(100-parseFloat(obj.currentArea.el.style.width), p);
							obj.currentArea.set('left',p);
						}
						break;
					case 38:
					case 40:
						var add = 1;
						if (ev.keyCode==38) {
							add = -1;
						}
						if (obj.altPressed && obj.currentArea.get('hotspotX')!=null) {
							var v = parseFloat(obj.currentArea.$hotspot.css('top'));
							var p = v*obj.currentArea.$el.height()/100 + add;
							p = p*100/obj.currentArea.$el.height();
							p = Math.max(0, p);
							p = Math.min(100, p);
							obj.currentArea.set('hotspotY',p);
						} else {
							var v = parseFloat(obj.currentArea.$el.css('top'));
							var p = v*obj.workspace.$workspace.height()/100 + add;
							p = p*100/obj.workspace.$workspace.height();
							p = Math.max(0, p);
							p = Math.min(100-parseFloat(obj.currentArea.el.style.height), p);
							obj.currentArea.set('top',p);
						}
						break;
					case 46:
						obj.$el.find('[rel="deleteArea"]').click();
						break;
					case 107:
					case 109:
						var add = 1;
						if (ev.keyCode==109) {
							add = -1;
						}
						if (obj.altPressed) {
							var v = parseFloat(obj.currentArea.$el.css('height'));
							var p = v*obj.workspace.$workspace.height()/100 + add;
							p = p*100/obj.workspace.$workspace.height();
							p = Math.min(100-parseFloat(obj.currentArea.el.style.top), p);
							obj.currentArea.set('height',p);
						} else {
							var v = parseFloat(obj.currentArea.$el.css('width'));
							var p = v*obj.workspace.$workspace.width()/100 + add;
							p = p*100/obj.workspace.$workspace.width();
							p = Math.max(0, p);
							obj.currentArea.set('width',p);
						}
						break;
				}
			}
		})

		$(window).bind('keyup', function(ev) {
			if (ev.keyCode == 18) obj.altPressed = false;
		});
	}

	this.onInputChange = function(el) {
		var $t = $(el);
		var name = $t.attr('data-name');
		var value = $t.val();
		obj.currentArea.set(name, value);
	}

	this.appendInput = function(prop) {
		var obj = this;
		if (this.editableInEditor.indexOf(prop.name)!=-1) {
			return;
		}

		var $el = $('<'+prop.type+'>');
		$el.attr('data-name', prop.name);
		
		if (prop.editable == false) {
			$el.attr('readonly', true);
		}

		switch(prop.type) {
			case 'select':
				for (var i=0; i<prop.options.length; i++) {
					$el.append('<option value="'+prop.options[i]+'">'+prop.options[i]+'</option>');
				}
				break;
			case 'textarea':
				$el.attr('rows', 1);
				break;	
		}

		if (obj.inputs==null) {
			$el.attr('data-flatlander-inputid', 0);
			obj.inputs = $el;
		} else {
			$el.attr('data-flatlander-inputid', obj.inputs.length);
			obj.inputs = obj.inputs.add($el);
		}

		obj.inputsCallbacks[$el.attr('data-flatlander-inputid')] = {
			onClick: [obj.onInputChange, prop.onClick || function() {}],
			onChange: [obj.onInputChange, prop.onChange || function() {}],
		}
		
		if ($el.attr('type')!='hidden') {
			var name = $el.attr('data-name');
			var label = '<label>'+name+':</label>';
			obj.$el.find('.fl-editorContainer').append(label);
		}
		
		obj.$el.find('.fl-editorContainer').append($el);

		obj.inputs.unbind('click.flatlandereditor change.flatlandereditor keyup.flatlandereditor').bind('click.flatlandereditor', function() {
			var el = this;
			var id = $(this).attr('data-flatlander-inputid');
			for (var i=0; i<obj.inputsCallbacks[id]['onClick'].length; i++) {
				obj.inputsCallbacks[id]['onClick'][i](el);
			}
		}).bind('change.flatlandereditor keyup.flatlandereditor', function() {
			var el = this;
			var id = $(this).attr('data-flatlander-inputid');
			for (var i=0; i<obj.inputsCallbacks[id]['onChange'].length; i++) {
				obj.inputsCallbacks[id]['onChange'][i](el);
			}
		})
	}

	this.bindEvents();
	/*this.$el.draggable({
		handle: '.fl-closeEditor'
	}).resizable();*/
	this.$el.find('.fl-layers').sortable({
		update: function(ev) {
			obj.sort();
			$(ev.toElement).click();
		}
	});
}