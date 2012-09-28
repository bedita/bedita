<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1129309932504ef5e2455de7-57024419%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1158f17ae2833b85ea1536ba5d47394aba97c068' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file_list.tpl',
      1 => 1347293010,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1129309932504ef5e2455de7-57024419',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'relation' => 0,
    'attach' => 0,
    'item' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e2606d53_11988840',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e2606d53_11988840')) {function content_504ef5e2606d53_11988840($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.disable.text.select",true);?>


<script type="text/javascript">
    $(function() {
        $('.disableSelection').disableTextSelect();
    });	
	
var urlGetObj		= '<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/get_item_form_by_id");?>
' ;
var urlGetAllItemNoAssoc = '<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/showStreams");?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '0' : $tmp);?>
';
var containerItem = "#multimediaItems";

function commitUploadItem(IDs) {

	var currClass =  $(".multimediaitem:last").attr("class");
	//alert(currClass);
	
	for(var i=0 ; i < IDs.length ; i++)
	{
		var id = escape(IDs[i]) ;
		var emptyDiv = "<div id='item_" + id + "' class=' " + currClass + " gold '><\/div>";
		$(emptyDiv).load(
			urlGetObj, { 'id': id, 'relation':"attach" }, function (responseText, textStatus, XMLHttpRequest)
			{
				$("#loading").hide();
				$(containerItem).append(this).fixItemsPriority(); 
				$(containerItem).sortable("refresh");
			}
		)
	}	
}


function showResponse(data) {

	if (data.UploadErrorMsg) {
		$("#loading").hide();
		//$("#addmultimedia").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
		showMultimediaAjaxError(null, data.UploadErrorMsg, null);
	} else {
		var tmp = new Array() ;
		var countFile = 0; 
		$.each(data, function(entryIndex, entry) {
			tmp[countFile++] = entry['fileId'];
		});

		commitUploadItem(tmp);
	}

		$("#addmultimedia").find("input[type=text]").attr("value", "");
		$("#addmultimedia").find("input[type=file]").attr("value", "");
		$("#addmultimedia").find("textarea").attr("value", "");
}

function showMultimediaAjaxError(XMLHttpRequest, textStatus, errorThrown) {
	var submitUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/showAjaxMessage/');?>
";
	var errorMsg = textStatus;
	if (XMLHttpRequest != null && XMLHttpRequest.responseText) {
		errorMsg += "<br/><br/> " + XMLHttpRequest.responseText;
	}
	$("#messagesDiv").load(submitUrl,{ "msg":errorMsg,"type":"error" }, function() {
		$("#loading").hide();
	});
}

function resetError() {
	$("#addmultimedia").find("label").remove();
	$("#loading").show();
}

// Remove item from queue
function removeItem(divId) {
	$("#" + divId).remove() ;
	$("#multimediaItems").fixItemsPriority();
}



// JQuery stuff
$(document).ready(function()
{  
	var optionsForm = {
		beforeSubmit:	resetError,
		success:		showResponse,  // post-submit callback  
		dataType:		'json',        // 'xml', 'script', or 'json' (expected server response type)
		error: showMultimediaAjaxError
	};

	$("#uploadForm").click(function() {
		optionsForm.url = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/files/uploadAjax');?>
"; // override form action
		$('#updateForm').ajaxSubmit(optionsForm);
		return false;
	});

	$("#uploadFormMedia").click(function() {
		optionsForm.url = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/files/uploadAjaxMediaProvider');?>
"; // override form action
		$('#updateForm').ajaxSubmit(optionsForm);
		return false;
	});

	$(containerItem).sortable ({
		distance: 5,
		opacity:0.7,
		//handle: $(".multimediaitem").add(".multimediaitem img"), //try to fix IE7 handle on images, but don't work acc!
		sort: checkDragDropTarget,
		start: showBodyDropTarget,
		stop: hideBodyDropTarget,
		update: $(this).fixItemsPriority
	}).css("cursor","move");

	/* Drag&drop di elementi multimediali nel testo */

	var targets = {}; //aree di rilascio, definite in form_textbody.tpl
	var windowTopPosition = '';
	var editorTopPosition = '';
	var editorHeight = '';
	var textToReplace = '';
	
	/*$(document).bind('instanceReady.ckeditor', function(e){
		if(e.target.name == 'data[body]'){
			var $_div = $('<div>');
			$_div.html($("textarea[name='data[body]']").val())
				.find('.placeref')
				.each(function(){
					var $_placerefLink = $(this);
					var nickname = $_placerefLink.attr('href');
					var imageUrl = $('.media_nickname[value='+nickname+']').siblings('.imagebox').find('img').attr('src');
					$_placerefLink.append('<img src="'+imageUrl+'" class="removeme" />')
				});

			$("textarea[name='data[body]']").val($_div.html());
		}
	});
	$("div.insidecol input[name='save']").preBind('click', function() {
		//if (CKEDITOR.instances['data[body]']!=='undefined'){ CKEDITOR.instances['data[body]'].destroy(true);};
		var $_div = $('<div>');
		$_div.html($("textarea[name='data[body]']").val())
			.find('.removeme')
			.remove();
		$("textarea[name='data[body]']").val($_div.html());
	});*/
	$(document).bind('instanceReady.ckeditor', function(e){
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
		//if(textToReplace.length>0){
		for(var targetName in targets){
			if(targets[targetName].hover){
				var attributesList = $.parseJSON($('.dropSubTarget[rel="'+targetName+'"]').attr('data-attributes'));
				var htmlAttributes = '';
				for(var attributeName in attributesList){
					htmlAttributes += ' ' + attributeName + '="' + attributesList[attributeName] + '"';
				}
				var optionsList = $.parseJSON($('.dropSubTarget[rel="'+targetName+'"]').attr('data-options'));
				var nickname = $(draggedElement.item).find('.media_nickname').val();
				var imageUrl = $(draggedElement.item).find('.imagebox img').attr('src');
				
				for(var attributeName in attributesList){
					htmlAttributes += ' ' + attributeName + '="' + attributesList[attributeName] + '"';
				}

				if(typeof optionsList.selection !== 'undefined' && optionsList.selection == 'required'){
					if(textToReplace==''){
						textToReplace = nickname;
					}
				}
				if(typeof optionsList.object !== 'undefined'){
					switch(optionsList.object){
						case 'a':
							if(typeof optionsList.type !== 'undefined'){
								if(optionsList.type=='wrap'){
									element = '<a href="'+nickname+'"'+htmlAttributes+'>' + textToReplace + '</a>';
								} else {
									element = '<a href="'+nickname+'"'+htmlAttributes+'></a>';
								}
							} else {
								element = '<a href="'+nickname+'"'+htmlAttributes+'>' + textToReplace + '</a>';
							}
							break;
						case 'img':
							if(typeof optionsList.type !== 'undefined'){
								switch(optionsList.type){
									case 'delete':
										element = '<img src="'+imageUrl+' id="'+nickname+'"'+htmlAttributes+' />';
										break;
									case 'append':
										CKEDITOR.instances['data[body]'].insertText(textToReplace);
										element = '<img src="'+imageUrl+'" id="'+nickname+'"'+htmlAttributes+' />';
										break;
								}
							} else {
								element = '<span>' + textToReplace + '</span><img src="'+imageUrl+'" id="'+nickname+'"'+htmlAttributes+' />';
							}
							break;
					}
				} else {
					element = textToReplace + '<img src="'+imageUrl+'" id="'+nickname+'"'+htmlAttributes+' />';
				}

				var editorElement = CKEDITOR.dom.element.createFromHtml(element);
				CKEDITOR.instances['data[body]'].insertElement(editorElement);
			}
		}
		textToReplace = '';
		//}
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
</script>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Multimedia items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>	

<div id="multimedia">
	
<fieldset id="multimediaItems" style="margin-left:10px">	

<img class="multimediaitemToolbar viewsmall" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconML-small.png" />
<img class="multimediaitemToolbar viewthumb" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconML-thumb.png" />

<hr />
<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
][0][switch]" value="<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
" />

<?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['attach']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value){
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
	<div class="multimediaitem itemBox <?php if ($_smarty_tpl->tpl_vars['item']->value['status']!="on"){?> off<?php }?> XdisableSelection" id="item_<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
">
			<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_file_item',array('item'=>$_smarty_tpl->tpl_vars['item']->value,'relation'=>$_smarty_tpl->tpl_vars['relation']->value));?>

	</div>
<?php } ?>

</fieldset>


<fieldset id="addmultimedia">	

<div id="loading" style="clear:both" class="multimediaitem itemBox small">&nbsp;</div>

	<table class="htab">
	<tr>
		<td rel="uploadItems"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
upload new items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td rel="urlItems"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add by url<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td rel="repositoryItems" id="reposItems"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select from archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
	</tr>
	</table>
	
<div class="htabcontainer" id="addmultimediacontents">

	<div class="htabcontent" id="uploadItems">
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_upload_multi');?>

	</div>

	
	<div class="htabcontent" id="urlItems">
		
		<table style="margin-bottom:20px">
		<tr>
			<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
url<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
			<td><input type="text" style="width:270px;" name="uploadByUrl[url]" /></td>
		</tr>
		<tr>
			<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
			<td><input type="text" style="width:270px;" name="uploadByUrl[title]" /></td>
		</tr>
		<tr>
			<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
			<td><textarea style="width:270px; min-height:16px; height:16px;" class="autogrowarea" name="uploadByUrl[description]"></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="button" style="width:160px; margin-top:15px" id="uploadFormMedia" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
			</td>
		</tr>
		</table>
	</div>


	<div class="htabcontent" id="repositoryItems">
		<div id="ajaxSubcontainer"></div>
	</div>

</div>

</fieldset>

</div>




<?php }} ?>