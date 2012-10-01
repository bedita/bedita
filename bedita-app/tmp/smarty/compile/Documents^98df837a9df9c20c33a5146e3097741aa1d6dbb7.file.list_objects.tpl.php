<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:43
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/list_objects.tpl" */ ?>
<?php /*%%SmartyHeaderCode:563320007504e09ac105d64-78231466%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98df837a9df9c20c33a5146e3097741aa1d6dbb7' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/list_objects.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '563320007504e09ac105d64-78231466',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e09ac6b3aa4_03602159',
  'variables' => 
  array (
    'html' => 0,
    'beToolbar' => 0,
    'htmlAttributes' => 0,
    'objects' => 0,
    'conf' => 0,
    'tree' => 0,
    'view' => 0,
    'sectionSel' => 0,
    'beTree' => 0,
    'pubSel' => 0,
    'filter_section_name' => 0,
    'categories' => 0,
    'named_arr' => 0,
    'key' => 0,
    'category' => 0,
    'filter_category_name' => 0,
    'filter_category_id' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e09ac6b3aa4_03602159')) {function content_504e09ac6b3aa4_03602159($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
if (!is_callable('smarty_function_html_options')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/function.html_options.php';
?><script type="text/javascript">
<!--
var message = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete the item?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" ;
var messageSelected = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete selected items?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" ;
var urls = Array();
urls['deleteSelected'] = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('deleteSelected/');?>
";
urls['changestatusSelected'] = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('changeStatusObjects/');?>
";
urls['copyItemsSelectedToAreaSection'] = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('addItemsToAreaSection/');?>
";
urls['moveItemsSelectedToAreaSection'] = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('moveItemsToAreaSection/');?>
";
urls['removeFromAreaSection'] = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('removeItemsFromAreaSection/');?>
";
urls['assocObjectsCategory'] = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('assocCategory/');?>
";
urls['disassocObjectsCategory'] = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('disassocCategory/');?>
";
var no_items_checked_msg = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No items selected<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
var sel_status_msg = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select a status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
var sel_category_msg = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select a category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
var sel_copy_to_msg = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select a destination to 'copy to'<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";

function count_check_selected() {
	var checked = 0;
	$('input[type=checkbox].objectCheck').each(function(){
		if($(this).attr("checked")) {
			checked++;
		}
	});
	return checked;
}

$(document).ready(function(){

	// avoid to perform double click
	$("a:first", ".indexlist .obj").click(function(e){ 
		e.preventDefault();
	});

	$(".indexlist .obj TD").not(".checklist").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );

	$("#deleteSelected").bind("click", function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		if(!confirm(messageSelected)) 
			return false ;	
		$("#formObject").attr("action", urls['deleteSelected']) ;
		$("#formObject").submit() ;
	});

	$("#assocObjects").click( function() {
		if(count_check_selected()==0) {
			alert(no_items_checked_msg);
			return false;
		}
		if($('#areaSectionAssoc').val() == "") {
			alert(sel_copy_to_msg);
			return false;
		}
		var op = ($('#areaSectionAssocOp').val()) ? $('#areaSectionAssocOp').val() : "copy";
		$("#formObject").attr("action", urls[op + 'ItemsSelectedToAreaSection']) ;
		$("#formObject").submit() ;
	});

	$(".opButton").click( function() {
		if(count_check_selected()==0) {
			alert(no_items_checked_msg);
			return false;
		}
		if(this.id.indexOf('changestatus') > -1) {
			if($('#newStatus').val() == "") {
				alert(sel_status_msg);
				return false;
			}
		}
		if(this.id == 'assocObjectsCategory') {
			if($('#objCategoryAssoc').val() == "") {
				alert(sel_category_msg);
				return false;
			}
		}
		if(this.id == 'disassocObjectsCategory') {
			$('#objCategoryAssoc').attr('value',$('#filter_category').val());
		}
		$("#formObject").attr("action",urls[this.id]) ;
		$("#formObject").submit() ;
	});
});

//-->
</script>	
	
<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>

	<table class="indexlist">
	<?php $_smarty_tpl->_capture_stack[0][] = array("theader", null, null); ob_start(); ?>
		<thead>
		<tr>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('fixed','&nbsp;');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('title','title');?>
</th>
			<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('id','id');?>
</th>
			<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('status','status');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('modified','modified');?>
</th>
			<th style="text-align:center">
				<?php echo smarty_function_assign_associative(array('var'=>"htmlAttributes",'alt'=>"comments",'border'=>"0"),$_smarty_tpl);?>
 
				<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('num_of_comment','','iconComments.gif',$_smarty_tpl->tpl_vars['htmlAttributes']->value);?>

			</th>			
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('lang','lang');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('num_of_editor_note','notes');?>
</th>
		</tr>
		</thead>
	<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		
		<?php echo Smarty::$_smarty_vars['capture']['theader'];?>

	
		<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['name'] = "i";
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['objects']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total']);
?>
		
		<tr class="obj <?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['status'];?>
">
			<td class="checklist">
			<?php if (!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['start_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['start_date'],"%Y%m%d"))>(smarty_modifier_date_format(time(),"%Y%m%d"))){?>
			
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object scheduled in the future<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFuture.png" style="height:28px; vertical-align:top;">
			
			<?php }elseif(!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['end_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['end_date'],"%Y%m%d"))<(smarty_modifier_date_format(time(),"%Y%m%d"))){?>
			
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object expired<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconPast.png" style="height:28px; vertical-align:top;">
			
			<?php }elseif((!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['start_date'])&&((smarty_modifier_date_format($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['start_date'],"%Y%m%d"))==(smarty_modifier_date_format(time(),"%Y%m%d"))))||(!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['end_date'])&&((smarty_modifier_date_format($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['end_date'],"%Y%m%d"))==(smarty_modifier_date_format(time(),"%Y%m%d"))))){?>
			
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object scheduled today<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconToday.png" style="height:28px; vertical-align:top;">

			<?php }?>
			
			<?php if (!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['num_of_permission'])){?>
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
permissions set<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconLocked.png" style="height:28px; vertical-align:top;">
			<?php }?>
			
			<?php if ((empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['fixed']))){?>
				<input style="margin-top:8px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
" />
			<?php }else{ ?>
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
fixed object<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFixed.png" style="margin-top:8px; height:12px;" />
			<?php }?>


			</td>
			<td style="min-width:300px">
				<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('view/');?>
<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
"><?php echo (($tmp = @smarty_modifier_truncate($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'],64))===null||$tmp==='' ? "<i>[no title]</i>" : $tmp);?>
</a>
				<div class="description" id="desc_<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
">
					nickname:<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['nickname'];?>
<br />
					<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['description'];?>

				</div>
			</td>
			<td class="checklist detail" style="text-align:left; padding-top:4px;">
				<a href="javascript:void(0)" onclick="$('#desc_<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
').slideToggle(); $('.plusminus',this).toggleText('+','-')">
				<span class="plusminus">+</span>			
				&nbsp;
				<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>

				</a>	
			</td>
			<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['status'];?>
</td>
			<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</td>
			<td style="text-align:center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['num_of_comment'])===null||$tmp==='' ? 0 : $tmp);?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['lang'];?>
</td>
			<td><?php if ((($tmp = @$_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['num_of_editor_note'])===null||$tmp==='' ? '' : $tmp)){?><img src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconNotes.gif" alt="notes" /><?php }?></td>
		</tr>
		
		
		
		<?php endfor; else: ?>
		
			<tr><td colspan="100" style="padding:30px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No items found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td></tr>
		
		<?php endif; ?>
		
<?php if (($_smarty_tpl->getVariable('smarty')->value['section']['i']['total'])>=10){?>
		
			<?php echo Smarty::$_smarty_vars['capture']['theader'];?>

			
<?php }?>


</table>

<br />
	
<?php if (!empty($_smarty_tpl->tpl_vars['objects']->value)){?>

<div style="white-space:nowrap">
	
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
(un)select all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	&nbsp;&nbsp;&nbsp
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Go to page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changePageSelect('pagSelectBottom');?>
 
	&nbsp;
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;
	<?php if (($_smarty_tpl->tpl_vars['beToolbar']->value->pages())>0){?>
	<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->last($_smarty_tpl->tpl_vars['beToolbar']->value->pages(),'',$_smarty_tpl->tpl_vars['beToolbar']->value->pages());?>

	<?php }else{ ?>1<?php }?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Dimensions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changeDimSelect('selectTop');?>
 &nbsp;
	
	&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;
	<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->next('next','','next');?>
  <span class="evidence"> &nbsp;</span>	
	| &nbsp;&nbsp;
	<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->prev('prev','','prev');?>
  <span class="evidence"> &nbsp;</span>
</div>

<br />

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Bulk actions on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;<span class="selecteditems evidence"></span> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
selected records<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<div>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
change status to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: 	<select style="width:75px" id="newStatus" name="newStatus">
								<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['conf']->value->statusOptions),$_smarty_tpl);?>

							</select>
			<input id="changestatusSelected" type="button" value=" ok " class="opButton"/>
	<hr />
	
	<?php if (!empty($_smarty_tpl->tpl_vars['tree']->value)){?>
		<?php $_smarty_tpl->tpl_vars['named_arr'] = new Smarty_variable($_smarty_tpl->tpl_vars['view']->value->params['named'], null, 0);?>
		<?php if (empty($_smarty_tpl->tpl_vars['sectionSel']->value['id'])){?>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
copy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<?php }else{ ?>
			<select id="areaSectionAssocOp" name="areaSectionAssocOp" style="width:75px">
				<option value="copy"> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
copy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 </option>
				<option value="move"> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
move<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 </option>
			</select>
		<?php }?>
		&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:  &nbsp;

		<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
		<?php echo $_smarty_tpl->tpl_vars['beTree']->value->option($_smarty_tpl->tpl_vars['tree']->value);?>

		</select>

		<input type="hidden" name="data[source]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['sectionSel']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
		<input id="assocObjects" type="button" value=" ok " />
		<hr />
		
		<?php if (!empty($_smarty_tpl->tpl_vars['sectionSel']->value['id'])){?>
			<?php $_smarty_tpl->tpl_vars['filter_section_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['sectionSel']->value['id'], null, 0);?>
			<?php $_smarty_tpl->tpl_vars['filter_section_name'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['pubSel']->value['title'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['sectionSel']->value['title'] : $tmp), null, 0);?>
			<input id="removeFromAreaSection" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove selected from<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 '<?php echo $_smarty_tpl->tpl_vars['filter_section_name']->value;?>
'" class="opButton" />
			<hr/>
		<?php }?>
	<?php }?>

	<?php if (!empty($_smarty_tpl->tpl_vars['categories']->value)){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<select id="objCategoryAssoc" class="objCategoryAssociation" name="data[category]">
		<option value="">--</option>
		<?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value){
$_smarty_tpl->tpl_vars['category']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['category']->key;
?>
		<?php if (!empty($_smarty_tpl->tpl_vars['named_arr']->value['category'])&&($_smarty_tpl->tpl_vars['key']->value==$_smarty_tpl->tpl_vars['named_arr']->value['category'])){?><?php $_smarty_tpl->tpl_vars['filter_category_name'] = new Smarty_variable($_smarty_tpl->tpl_vars['category']->value, null, 0);?><?php }?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['category']->value;?>
</option>
		<?php } ?>
		</select>
		<input id="assocObjectsCategory" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add association<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="opButton"/>
		<hr />
		<?php if (!empty($_smarty_tpl->tpl_vars['named_arr']->value['category'])){?>
			<?php $_smarty_tpl->tpl_vars['filter_category_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['named_arr']->value['category'], null, 0);?>
			<input id="disassocObjectsCategory" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove selected from category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 '<?php echo $_smarty_tpl->tpl_vars['filter_category_name']->value;?>
'" class="opButton" />
			<input id="filter_category" type="hidden" name="filter_category" value="<?php echo $_smarty_tpl->tpl_vars['filter_category_id']->value;?>
" />
			<hr />
		<?php }?>
	<?php }?>
	
	<input id="deleteSelected" type="button" value="X <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Delete selected items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
</div>

<?php }?>

</form>

<br />
<br />
<br />
<br /><?php }} ?>