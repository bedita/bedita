<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/menuright.tpl" */ ?>
<?php /*%%SmartyHeaderCode:819805065504dfd9ca1e941-90648147%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '544b2d1efbfc9339c8c8625338aaf48064c9ad3a' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/menuright.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '819805065504dfd9ca1e941-90648147',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9cb365b2_61766954',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'note' => 0,
    'params' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9cb365b2_61766954')) {function content_504dfd9cb365b2_61766954($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_block_bedev')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.bedev.php';
?>

<script type="text/javascript">
var urlLoadNote = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/loadNote');?>
";
var urlDelNote = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/deleteNote');?>
";
var comunicationErrorMsg = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Communication error<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
var confirmDelNoteMsg = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete the note?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";


$(document).ready( function (){
	$("#editornotes").prev(".tab").BEtabsopen();

	var optionsNoteForm = {
		beforeSubmit: function() { $("#noteloader").show();},
		success: showNoteResponse,
		dataType: "json",
		resetForm: true,
		error: function() {
			alert(comunicationErrorMsg);
			$("#noteloader").hide();
		}
	}; 
	$("#saveNote").ajaxForm(optionsNoteForm);
	
	$("#listNote").find("input[name=deletenote]").click(function() {
		refreshNoteList($(this));
	});
});	

function showNoteResponse(data) {
	if (data.errorMsg) {
		alert(data.errorMsg);
		$("#noteloader").hide();
	} else {
		var emptyDiv = "<div><\/div>";
		$(emptyDiv).load(urlLoadNote, data, function() {
			$("#listNote").prepend(this);
			$("#noteloader").hide();
			$(this).find("input[name=deletenote]").click(function() {
				refreshNoteList($(this));
			});
		});
	}
}

function refreshNoteList(delButton) {
	var div = delButton.parents("div:first");
	var postdata = { id: delButton.attr("rel")};
	if (confirm(confirmDelNoteMsg)) {
		$.ajax({ 
			type: "POST",
			url: urlDelNote,
			data: postdata,
			dataType: "json",
			beforeSend: function() { $("#noteloader").show();},
			success: function(data){ 
				if (data.errorMsg) { 
					alert(data.errorMsg);
					$("#noteloader").hide();
				} else {
					$("#noteloader").hide();
					div.remove();
				}
			},
			error: function() {
				alert(comunicationErrorMsg);
				$("#noteloader").hide();
			}
		});
	}
}

</script>


<div class="quartacolonna">	

<?php if (!empty($_smarty_tpl->tpl_vars['object']->value)){?>

	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Notes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
 
	<div id="editornotes" style="margin-top:-10px; padding:10px; background-color:white;">
	
	<table class="ultracondensed" style="width:100%"><tr><td class="author">you</td><td class="date">now</td><td><img src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconNotes.gif" alt="notes" /></td></tr></table><form id="saveNote" action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/saveNote');?>
" method="post"><input type="hidden" name="data[object_id]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['id'];?>
"/><textarea id="notetext" name="data[description]" class="autogrowarea editornotes"></textarea><input type="submit" style="margin-bottom:10px; margin-top:5px" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
send<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" /></form><div class="loader" id="noteloader" style="clear:both">&nbsp;</div><div id="listNote"><?php if ((!empty($_smarty_tpl->tpl_vars['object']->value['EditorNote']))){?><?php  $_smarty_tpl->tpl_vars["note"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["note"]->_loop = false;
 $_from = array_reverse($_smarty_tpl->tpl_vars['object']->value['EditorNote']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["note"]->key => $_smarty_tpl->tpl_vars["note"]->value){
$_smarty_tpl->tpl_vars["note"]->_loop = true;
?><?php echo smarty_function_assign_associative(array('var'=>"params",'note'=>$_smarty_tpl->tpl_vars['note']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['view']->value->element('single_note',$_smarty_tpl->tpl_vars['params']->value);?>
<?php } ?><?php }?></div>
	</div>

<?php }?>

	<?php $_smarty_tpl->smarty->_tag_stack[] = array('bedev', array()); $_block_repeat=true; echo smarty_block_bedev(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Test stuff<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
		<div id="test" style="padding:10px; background-color:white;">
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('BEiconstest');?>

		</div>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_bedev(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


</div><?php }} ?>