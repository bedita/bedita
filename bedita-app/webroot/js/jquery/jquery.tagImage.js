;(function($) {

	/* Funzione principale: mantiene i contesti delle immagini separate */

	// Richiamata da methods.init
	var tagImage = function(tagged){
		
		// Preparo il contesto
		$_imageWrapper = tagged;
		//var mediaId = typeof $_imageWrapper.data('nickname') !== undefined ? $_imageWrapper.data('nickname') : $_imageWrapper.attr('id');
		var mediaId = $_imageWrapper.data('mediaid');
		var $_img = $_imageWrapper.find('img');
		var mediaWidth = parseInt($_img.width());
		var mediaHeight = parseInt($_img.height());
		var nodesNames = ['n','ne','e','se','s','sw','w','nw','c'];
		function findNodesCoords(boxXPx,boxYPx,boxWidthPx,boxHeightPx){
			return [
				[boxXPx+boxWidthPx/2,boxYPx], //n
				[boxXPx+boxWidthPx,boxYPx], //ne
				[boxXPx+boxWidthPx,boxYPx+boxHeightPx/2], //e
				[boxXPx+boxWidthPx,boxYPx+boxHeightPx], //se
				[boxXPx+boxWidthPx/2,boxYPx+boxHeightPx], //s
				[boxXPx,boxYPx+boxHeightPx], //sw
				[boxXPx,boxYPx+boxHeightPx/2], //w
				[boxXPx,boxYPx], //nw
				[boxXPx+boxWidthPx/2,boxYPx+boxHeightPx/2] //c
			];
		}
		/*$_imageWrapper.css({
			width: parseInt($_img.width()) + 'px',
			height: parseInt($_img.height()) + 'px'
		})*/

		// Aggiungo la modale, esterna alle aree
		//$_imageWrapper.append('<div id="imageText-'+mediaId+'" class="imageText modal mainModal"><div class="content"></div><div class="closeText closeTextModal"></div></div>');
		$_imageWrapper.append('<div id="imageText-'+mediaId+'" class="imageText modal mainModal shadow10"><div class="content"></div></div>');
		
		var drawLine = function($_context,startX,startY,endX,endY){
			if(!$.browser.msie || ($.browser.msie && parseInt($.browser.version.substr(0,2))>8)){
				var $_line = $('<div>');
				$_line.attr('class','imageLine');
				//$('#image-'+mediaId).append($_line);
				$_context.append($_line);
				var angleRadians = Math.atan2(endY-startY,endX-startX);
				var angle = angleRadians/Math.PI*180;
				var distance = Math.abs(Math.sqrt((endX - startX)*(endX-startX) + (endY-startY)*(endY-startY))).toFixed();
				//console.log(angle, distance);
				var transformX = (startX+((distance/2)*Math.cos(angleRadians) - (distance/2))).toFixed();
				var transformY = (startY+((distance/2)*Math.sin(angleRadians))).toFixed();
				var transform = 'translate(' + transformX + 'px, '+ transformY+'px) rotate('+angle+'deg)';
				$_line.css({
					width: distance + 'px',
					transform: transform,
					'-webkit-transform': transform,
					'-moz-transform': transform,
					'-o-transform': transform,
					'-ms-transform': transform
				})
			}
		};

		// Rende visibili tooltip e postit, popola i modal
		var showAreaText = function($_target){
			var elementParts = $_target.attr('id').split('-')
			var elementId = elementParts[elementParts.length-1];
			var type = $_target.attr('rel');
			$_imageText = $('#imageTextA-'+mediaId+'-'+elementId);
			switch(type){
				case 'modal':
					var $_modalText = $_target.siblings('.mainModal');//$_imageWrapper.find('.mainModal');
					var text = $_imageText.html();
					$('.imageLine', $_imageWrapper).remove();
					$('.imageTarget[rel="modal"]',$_imageWrapper).not($_target).removeClass('opened');
					$_modalText
						.show()
						.children('.content')
						.html(text);
					var startX = $_modalText.position().left;
					var startY = $_modalText.position().top+parseInt($_modalText.height());;
					
					if($_target.hasClass('dotted') || $_target.hasClass('hidden')){
						var endX = $_target.position().left+parseInt($_target.width())/2;
						var endY = $_target.position().top+parseInt($_target.height())/2;
						if(endY<startY){
							startY = $_modalText.position().top;
						}
					} else {
						var endX = $_target.position().left+parseInt($_target.width())/2;
						var endY = $_target.position().top;
						if(endY<startY){
							endY = $_target.position().top +parseInt($_target.height()); 
						}
						if(endY<startY){
							startY = $_modalText.position().top;
						}
					}

					drawLine($_target.parent(),endX,endY,startX,startY);
					break;
				case 'tooltip':
				case 'postit':
					$_imageText.show();
					$_target.css('z-index', ++highestZIndex);
					break;
			}
		}
		// Nasconde tooltip e postit, chiude i modal
		var hideAreaText = function($_target){
			var elementParts = $_target.attr('id').split('-')
			var elementId = elementParts[elementParts.length-1];
			var $_imageText = $('#imageTextA-'+mediaId+'-'+elementId);
			var type = $_imageText.attr('ref');
			if(type=='modal'){
				closeTextModal(mediaId);
			} else {
				$_target.css('z-index', $_target.data('zIndex'));
				$_imageText.hide();
			}
		}
		// Chiude i modal
		var closeTextModal = function(mediaId){
			$_mainModal = $('.mainModal', '#image-'+mediaId);
			$('.imageLine', $_mainModal.parent()).remove();
			$_mainModal.hide().find('.content').empty();
			$('.imageTarget[rel="modal"]', $_mainModal.parent()).removeClass('opened');
		}
		
		// Click chiusura della modale
		$('.mainModal').bind('click.tagImage', function (e){
			e.preventDefault();
			e.stopPropagation();
			closeTextModal(mediaId);
		});
			
		// Rende visibili le caption
		var showCaptionText = function($_target){
			var elementParts = $_target.attr('id').split('-')
			var elementId = elementParts[elementParts.length-1];
			var type = $_target.attr('rel');
			$_imageText = $('#imageTextC-'+mediaId+'-'+elementId);
			$_imageText.show();
			$_target.find('.captionLine').show();
			$_target.find('.captionLineConnection').show();
			$_target.css('z-index', ++highestZIndex);
		}
		// Nasconde tooltip e postit, chiude i modal
		var hideCaptionText = function($_target){
			var elementParts = $_target.attr('id').split('-')
			var elementId = elementParts[elementParts.length-1];
			var $_imageText = $('#imageTextC-'+mediaId+'-'+elementId);
			var type = $_imageText.attr('ref');
			$_target.css('z-index', $_target.data('zIndex'));
			$_target.find('.captionLine').hide();
			$_target.find('.captionLineConnection').hide();
			$_imageText.hide();
		}
		// Registro lo z-index più alto, mi servirà dopo per lo switch
		var highestZIndex = 0; 

		var tags = $.parseJSON(unescape($_imageWrapper.data('tags')));
		var texts = $.parseJSON(unescape($_imageWrapper.data('texts')));

		//Creo le aree e i testi
		for(var elementNumber in tags.areas){
			if(tags.areas.hasOwnProperty(elementNumber)){
				var element = tags.areas[elementNumber];
				var elementId = 'imageTag-'+mediaId+'-'+elementNumber; //lascio stare element.id, si possono attribuire id diversi via data-mediaid
				var elementStyle = element.style;
				var elementType = element.type;
				var elementTitle = texts['a-'+elementNumber].title || texts[elementNumber].title;
				var elementDescription = texts['a-'+elementNumber].description || texts[elementNumber].description;
				var xPos = parseFloat(element.metrics.left)+(parseFloat(element.metrics.width)/2) < 50 ? 'left' : 'right';
				var yPos = parseFloat(element.metrics.top)+(parseFloat(element.metrics.height)/2) < 50 ? 'top' : 'bottom';
				
				var newDiv = '<div title="'+elementTitle+'" id="'+elementId+'" rel="'+elementType+'" class="imageTarget '+elementStyle+'">';

				// Le classi e gli stili del testo possono cambiare
				switch(elementType){
					case 'modal':
						newDiv += '<div id="imageTextA-'+mediaId+'-'+elementNumber+'" ref="'+elementType+'" class="imageText shadow10 '+elementType+' '+yPos+'">';
						break;
					case 'tooltip':
						if(elementStyle == 'dotted'){
							var applyStyle = (yPos == 'top') ? 'top: 50%' : 'bottom: 50%';
						} else {
							var applyStyle = (yPos == 'top') ? 'top: 100%' : 'bottom: 100%';
						}
						newDiv += '<div id="imageTextA-'+mediaId+'-'+elementNumber+'" ref="'+elementType+'" class="imageText shadow10 '+elementType+' '+xPos+' '+yPos+'" style="'+applyStyle+'">';
						//newDiv += '<div class="closeText"></div>';
						break;
					case 'postit':
						newDiv += '<div id="imageTextA-'+mediaId+'-'+elementNumber+'" ref="'+elementType+'" class="imageText shadow10 '+elementType+' '+xPos+' '+yPos+'">';
						//newDiv += '<div class="closeText"></div>';
						break;
				}
							if(elementTitle!='Titolo'){
								newDiv += '<div class="imageTitle">'+elementTitle+'</div>';	
							}
							if(elementDescription!='<p>Descrizione</p>'){
								newDiv += '<div class="imageDescription">'+elementDescription+'</div>';
							}
						newDiv += '</div>';
					newDiv += '</div>';
				$_imageWrapper.append(newDiv);

				var $_target = $('#'+elementId);
				$_target
					.css({
						left: element.metrics.left + '%',
						top: element.metrics.top + '%',
						width: element.metrics.width + '%',
						height: element.metrics.height + '%',
						zIndex: element.metrics.zIndex
					})
					.data('zIndex', element.metrics.zIndex); // registro lo zIndex come data per lo switch

				if(element.metrics.zIndex>highestZIndex){
					highestZIndex = element.metrics.zIndex;
				}

				// Click sul target - faccio il bind qui per poter fare il trigger in caso di visibilità iniziale
				$_target.bind('click.tagImage',function(e){
					e.stopPropagation();
					e.preventDefault();
					if(!$(this).hasClass('opened')){
						$(this).addClass('opened');
						showAreaText($(this));
					} else {
						if($(e.target).is('.imageTarget,.closeText')){
							$(this).removeClass('opened');
							hideAreaText($(this));
						}
						$(this).removeClass('opened');
						hideAreaText($(this));
					}
				})

				// Se l'elemento deve essere visibile dall'inizio
				if(element.visible=='true'){
					$_target.trigger("click");
				}
			}
		}

		//Creo i tiranti e i testi
		if(tags.hasOwnProperty('points')){
			for(var elementNumber in tags.points){
				if(tags.points.hasOwnProperty(elementNumber)){
					var element = tags.points[elementNumber];
					var elementId = 'imageCaption-'+mediaId+'-'+elementNumber; //lascio stare element.id, si possono attribuire id diversi via data-mediaid
					var elementType = 'caption';
					var elementStyle = 'caption';
					var elementSize = element.size;
					var elementNode = element.node;
					var elementTitle = texts['c-'+elementNumber].title;
					var elementDescription = texts['c-'+elementNumber].description;
					var metrics = element.metrics;

					var newDiv = '<div title="'+elementTitle+'" id="'+elementId+'" rel="'+elementType+'" class="imageCaption hidden">';
					newDiv += '	<div class="captionHotspot" style="z-index:4;left: '+metrics.hotspotX+'%; top:'+metrics.hotspotY+'%"></div>';
					newDiv += '	<div class="captionLine" style="z-index:1;"></div>'
					newDiv += '	<div class="captionLineConnection" style="z-index:3"></div>';
					newDiv += '	<div id="imageTextC-'+mediaId+'-'+elementNumber+'" ref="'+elementType+'" class="imageText shadow10 '+elementSize+' '+elementType+'" style="z-index: 2; left: '+ metrics.pointX+'%; top:'+metrics.pointY+'%">';
					if(elementTitle!='Titolo'){
						newDiv += '		<div class="imageTitle">'+elementTitle+'</div>';	
					}
					if(elementDescription!='<p>Descrizione</p>'){
						newDiv += '		<div class="imageDescription">'+elementDescription+'</div>';
					}
					newDiv += '	</div>';
					newDiv += '</div>';
					var nodeIndex = 0;
					for(var i = 0; i < nodesNames.length; i++){
						if(nodesNames[i]==elementNode){
							nodeIndex = i;
						}
					}
					$_imageWrapper.append(newDiv);
					var $_target = $('#'+elementId);
					var captionXPx = metrics.pointX * mediaWidth / 100;
					var captionYPx = metrics.pointY * mediaHeight / 100;
					var hotspotXPx = metrics.hotspotX * mediaWidth / 100;
					var hotspotYPx = metrics.hotspotY * mediaHeight / 100;
					var $_caption = $_target.find('.caption');
					var captionWidthPx = $_caption.width();
					var captionHeightPx = $_caption.height();
					var captionNodes = findNodesCoords(captionXPx-captionWidthPx/2,captionYPx-captionHeightPx/2,captionWidthPx,captionHeightPx);
					/*for(var i = 0; i<captionNodes.length; i++){
						$_imageWrapper.append('<div style="height:4px;width:4px;background-color: #ff00ff; position: absolute; left:'+captionNodes[i][0]+'px; top:'+captionNodes[i][1]+'px">')
					}*/

					nodeXPx = captionNodes[nodeIndex][0];
					nodeYPx = captionNodes[nodeIndex][1];
					$_target.children('.captionLineConnection').css({ left: nodeXPx * 100 / mediaWidth + '%', top: nodeYPx * 100 / mediaHeight +  '%'});
					$_caption.css('margin-top', -captionHeightPx/2);
					var angleRadians = Math.atan2(nodeYPx-hotspotYPx,nodeXPx-hotspotXPx);
					var angle = angleRadians/Math.PI*180;
					var distance = Math.sqrt(Math.pow(nodeXPx - hotspotXPx,2) + Math.pow(nodeYPx - hotspotYPx,2));
					var transformX = (hotspotXPx+((distance/2)*Math.cos(angleRadians) - (distance/2))).toFixed();
					var transformY = (hotspotYPx+((distance/2)*Math.sin(angleRadians))).toFixed();
					var transform = 'translate(' + transformX + 'px, '+ transformY+'px) rotate('+angle+'deg)';
					$_target
						.data('zIndex', element.metrics.zIndex)
						.children('.captionLine').css({
							width: distance + 'px',
							'-webkit-transform': transform,
							'-moz-transform': transform,
							'-ms-transform': transform,
							'-o-transform': transform,
							transform: transform
						});
						
					if(metrics.zIndex>highestZIndex){
						highestZIndex = metrics.zIndex;
					}

					// Click sul target - faccio il bind qui per poter fare il trigger in caso di visibilità iniziale
					$_target.bind('click.tagImage',function(e){
						e.stopPropagation();
						e.preventDefault();
						if(!$(this).hasClass('opened')){
							$(this).addClass('opened');
							showCaptionText($(this));
						} else {
							/*if($(e.target).is('.imageTarget,.closeText')){
								$(this).removeClass('opened');
								hideAreaText($(this));
							}*/
							$(this).removeClass('opened');
							hideCaptionText($(this));
						}
					})

					// Se l'elemento deve essere visibile dall'inizio
					if(element.visible=='true'){
						$_target.trigger("click");
					}
				}
			}
		}
	}

	var methods = {

		init : function(options) {
			return this.each(function(e){
				var $_tagged = $(this);
				var	data = $_tagged.data('tagImage');
				
				// Se non ha ancora aggiunto tag a quest'immagine
				if (!data) {
					var defaultOptions = {};
					if (options === undefined) {
						options = {};
					}
					var settings = $.extend(defaultOptions, options);
					$_tagged.data('tagImage', settings);
					data = $_tagged.data('tagImage');
					$_tagged.find('img').imagesLoaded(function(){
						tagImage($_tagged);
					})
				}
			});
		},

		destroy: function() {
			return this.each(function() {
				$(this).data('tagImage',undefined);
				$(this).removeData('tagImage');
				$(this).find(".imageTarget").remove();
				$(this).find(".imageCaption").remove();
				$(this).find(".imageText.modal").remove();
			});
		}
	};


	$.fn.tagImage = function(method) {
		if (methods[method]) {
			if (!$(this).data("tagImage") && method != "init") {
				$.error('jQuery.behighlight has to be initialized');
			}
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || ! method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' +  method + ' does not exist on jQuery.tagImage');
		}
	};

})(jQuery);
/* jQuery Images Loaded*/
(function(c,n){var k="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";c.fn.imagesLoaded=function(l){function m(){var b=c(h),a=c(g);d&&(g.length?d.reject(e,b,a):d.resolve(e));c.isFunction(l)&&l.call(f,e,b,a)}function i(b,a){b.src===k||-1!==c.inArray(b,j)||(j.push(b),a?g.push(b):h.push(b),c.data(b,"imagesLoaded",{isBroken:a,src:b.src}),o&&d.notifyWith(c(b),[a,e,c(h),c(g)]),e.length===j.length&&(setTimeout(m),e.unbind(".imagesLoaded")))}var f=this,d=c.isFunction(c.Deferred)?c.Deferred():
0,o=c.isFunction(d.notify),e=f.find("img").add(f.filter("img")),j=[],h=[],g=[];e.length?e.bind("load.imagesLoaded error.imagesLoaded",function(b){i(b.target,"error"===b.type)}).each(function(b,a){var e=a.src,d=c.data(a,"imagesLoaded");if(d&&d.src===e)i(a,d.isBroken);else if(a.complete&&a.naturalWidth!==n)i(a,0===a.naturalWidth||0===a.naturalHeight);else if(a.readyState||a.complete)a.src=k,a.src=e}):m();return d?d.promise(f):f}})(jQuery);

