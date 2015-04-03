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
	
	function hideBodyDropTarget(e, draggedElement){
		$('#bodyDropTarget').hide().find('div').hide();
		for(var targetName in targets){
			if(targets[targetName].hover) {
				var nickname = $(draggedElement.item).find('.rel_nickname').val();
				CKEDITOR.instances['data[body]'].execCommand('addPlaceholder', { id: nickname });
			}
		}
	};
	
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

	$("#reposItems").click( function () {
		$("#loading").show();
		$("#ajaxSubcontainer").show();
		$("#ajaxSubcontainer").load(urlGetAllItemNoAssoc, function() {
			$("#loading").hide();
			$('.selecteditems').text($(".objectCheck:checked").length);
			$(".selectAll").bind("click", function(e) {
				var status = this.checked;
				$(".objectCheck").each(function() { 
					this.checked = status; 
					if (this.checked) $(this).parents('TR').addClass('overChecked');
					else $(this).parents('TR').removeClass('overChecked');
				});
				$('.selecteditems').text($(".objectCheck:checked").length);
			}) ;
			$(".objectCheck").bind("click", function(e) {
				var status = true;
				$(".objectCheck").each(function() { 
					if (!this.checked) return status = false;
				});
				$(".selectAll").each(function() { this.checked = status;});
				$('.selecteditems').text($(".objectCheck:checked").length);
			}) ;
		});
	});
});