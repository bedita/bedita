/**
documents custom js
*/

$(window).load(function() {  

	var sortableOptions = {};
	if ($('textarea[name="data\[body\]"]').length > 0) {
		sortableOptions.sort = checkDragDropTarget;
		sortableOptions.start = showBodyDropTarget;
		sortableOptions.stop = hideBodyDropTarget;
	}

	$('#relationType_attach .indexlist tbody').sortable('option', sortableOptions);

	/* Drag&drop di elementi multimediali nel testo */

	var targets = {}; //aree di rilascio, definite in form_textbody.tpl
	var windowTopPosition = '';
	var editorTopPosition = '';
	var editorHeight = '';
	var textToReplace = '';
	
	$(document).bind('instanceReady.ckeditor', function(e,editor){
		if (editor.name == 'data[body]') {
			listenMode(editor);
		}
		$(".cke_button_image").attr('onclick','');
		$(".cke_button_image").bind('click', function() {
			openModal();
		});
	});

	function openModal(){
		if($('#multimediaModal').size()==0){

			$_modal = $('<div id="multimediaModal" class="modalWindow">')
			$_modal.css({
				position: 'fixed',
				top: '100px',
				left: '400px',
				width: '600px',
				zIndex: 1000,
				backgroundColor: '#ffffff'
			});

			$_modal.html($('#multimedia').html())
			$('body').append($_modal);
		} else {
			$('#multimediaModal').toggle();
		}
	}
	function showBodyDropTarget(e) {
		var $_editor = $('#cke_data\\[body\\]');
		var height = parseInt($_editor.outerHeight());
		var width= parseInt($_editor.outerWidth());
		editorTopPosition = $_editor.offset().top;
		editorHeight = height;
		$('#bodyDropTarget').css({
			width: width,
			height: height,
			marginBottom: -height,
			display: 'table'
		})
		textToReplace = CKEDITOR.instances['data[body]'].getSelection();
		textToReplace = textToReplace == null ? '' : textToReplace.getSelectedText();
		caretPosition = CKEDITOR.instances['data[body]'].getSelection().getRanges()[0]
		//if(textToReplace.length>0){
			$('#bodyDropTarget .allowed')
				.css('display','table-cell')
				.each(function(){
					var $_target = $(this);
					var targetName = $_target.attr('rel');
					targets[targetName] = {
						width: parseInt($_target.width()),
						height: parseInt($_target.height()),
						left: $_target.offset().left,
						top: $_target.offset().top
					};
				});
			windowTopPosition = $(window).scrollTop();
		//}
	};
	
	function hideBodyDropTarget(e,draggedElement){
		$('#bodyDropTarget').hide().find('div').hide();
		for(var targetName in targets){
			if(targets[targetName].hover){
				var attributesList = $.parseJSON($('.dropSubTarget[rel="'+targetName+'"]').attr('data-attributes'));
				var htmlAttributes = '';
				for(var attributeName in attributesList){
					htmlAttributes += ' ' + attributeName + '="' + attributesList[attributeName] + '"';
				}
				var optionsList = $.parseJSON($('.dropSubTarget[rel="'+targetName+'"]').attr('data-options'));
				var title = $(draggedElement.item).find('.assoc_obj_title > h4').text();
				var nickname = $(draggedElement.item).find('.rel_nickname').val();

				if(typeof optionsList.selection !== 'undefined' && optionsList.selection == 'required'){
					if(textToReplace == ''){
						textToReplace = (title != '') ? title : nickname;
					}
				}
				
				if (textToReplace == '') {
					textToReplace = '&#8203;';
				}
				
				element = ' <a href="'+nickname+'" '+htmlAttributes+'>' + textToReplace + '</a>';

				var editorElement = document.createElement('a');
				editorElement.innerHTML = element;
				CKEDITOR.instances['data[body]'].insertHtml(element);
				setPlaceCss(CKEDITOR.instances['data[body]']);
			}
		}
		textToReplace = '';
		
		//}
	};
	
	function listenMode(editor) {		
		editor.on('change',function(){
			setPlaceCss(this);
		});
		
		editor.on('mode', function(event) {
			if (editor.mode == "wysiwyg") {
				setPlaceCss(this);
			}
		});
		
		setPlaceCss(editor);
		
		$(document).on('keyup', '*', function() {
			setPlaceCss(editor);
		});
	}

	function setPlaceCss(editor) {
		var jph = $('iframe.cke_wysiwyg_frame').contents().find('A.placeholder, A.plaref, A[target=modal]');
		var style = '<style id="placeholderCss">';
		if (editor.mode == "wysiwyg") {
			jph.each(function() {
				var href = $(this).attr('href');
				var src = $('#relationType_attach .obj[data-benick="'+href+'"]').find('img').prop('src');
				style+=' A[href='+href+']:after{ background-image: url("'+src+'") } ';
			});
		}
		style+='</style>';
		$('iframe.cke_wysiwyg_frame').contents().find('head').find('#placeholderCss').remove();
		$('iframe.cke_wysiwyg_frame').contents().find('head').append(style);
	}

	function checkDragDropTarget(e){
		var mouseX = e.pageX;
		var mouseY = e.pageY;
		if(mouseY>editorTopPosition-50 && mouseY < editorTopPosition+editorHeight + 50){ 
			CKEDITOR.instances['data[body]'].focus();
		// area sensibile dell'editor perché venga visto come target
			$('#bodyDropTarget').css('display','table');

			//if(textToReplace.length>0){
				$('#bodyDropTarget .allowed').css('display','table-cell');
				if(windowTopPosition == $(window).scrollTop()){
					for(var targetName in targets){
						var $_target = $('.dropSubTarget[rel="'+targetName+'"]')
						if (mouseX>targets[targetName].left
							&& mouseX<targets[targetName].left+targets[targetName].width
							&& mouseY>targets[targetName].top
							&& mouseY<targets[targetName].top+targets[targetName].height){

							$_target.addClass('hover');
							targets[targetName].hover = true;
						} else {
							$_target.removeClass('hover');
							targets[targetName].hover = false;
						}
					}
				} else {
					windowTopPosition = $(window).scrollTop();
					for(var targetName in targets){
						var $_target = $('.dropSubTarget[rel="'+targetName+'"]')
						var offset =  $_target.offset();
						targets[targetName].left = offset.left;
						targets[targetName].top = offset.top;

						if (mouseX>offset.left
							&& mouseX<offset.left+targets[targetName].width
							&& mouseY>offset.top
							&& mouseY<offset.top+targets[targetName].height){

							$_target.addClass('hover');
							targets[targetName].hover = true;
						} else {
							$_target.removeClass('hover');
							targets[targetName].hover = false;
						}
					}
				}
			//} else {
			//	$('#bodyDropTarget .denied').css('display','table-cell');
			//}
		} else {
			$('#bodyDropTarget').hide().find('div').hide();
		}
	};

	/* Seems unused...
	$("#reposItems").click( function () {
		$("#loading").show();
		$("#ajaxSubcontainer").show();
		$("#ajaxSubcontainer").load(urlGetAllItemNoAssoc, function() {
			$("#loading").hide();
			$('.selecteditems').text($(".objectCheck:checked").length);
		});
	});
	*/
});