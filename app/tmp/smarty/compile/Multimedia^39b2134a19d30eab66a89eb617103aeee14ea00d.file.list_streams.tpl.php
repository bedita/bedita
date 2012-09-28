<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:56
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/list_streams.tpl" */ ?>
<?php /*%%SmartyHeaderCode:280470894504e1033a27306-45396294%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '39b2134a19d30eab66a89eb617103aeee14ea00d' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/list_streams.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '280470894504e1033a27306-45396294',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e1033e767e4_48834737',
  'variables' => 
  array (
    'html' => 0,
    'beToolbar' => 0,
    'objects' => 0,
    'item' => 0,
    'params' => 0,
    'view' => 0,
    'conf' => 0,
    'tree' => 0,
    'named_arr' => 0,
    'beTree' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e1033e767e4_48834737')) {function content_504e1033e767e4_48834737($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_function_math')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/function.math.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
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
var no_items_checked_msg = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No items selected<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
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
	
	$("#deleteSelected").click(function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		if(!confirm(message)) 
			return false ;
		$("#formObject").attr("action", urls['deleteSelected']) ;
		$("#formObject").submit() ;
	});

	$("#assocObjects").click( function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		var op = ($('#areaSectionAssocOp').val()) ? $('#areaSectionAssocOp').val() : "copy";
		$("#formObject").attr("action", urls[op + 'ItemsSelectedToAreaSection']) ;
		$("#formObject").submit() ;
	});

	$(".opButton").click( function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		$("#formObject").attr("action",urls[this.id]) ;
		$("#formObject").submit() ;
	});
});

//-->
</script>	

<style>
	.vlist { display:none }
</style>


<form method="post" action="" id="formObject">

	<div id="viewthumb">
	<table class="indexlist">
	<?php $_smarty_tpl->_capture_stack[0][] = array("theader", null, null); ob_start(); ?>
		<tr>
			<th colspan="2" nowrap>
				
				 <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:
			</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('id','id');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('title','Title');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('name','Name');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('mediatype','type');?>
</th>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
size<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('status','Status');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('modified','modified');?>
</th>
		</tr>
	<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		
		<?php echo Smarty::$_smarty_vars['capture']['theader'];?>


	</table>

	<br style="clear:both" />
	<?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['objects']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value){
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?><div class="multimediaitem itemBox<?php if ($_smarty_tpl->tpl_vars['item']->value['status']!="on"){?> off<?php }?>"><?php echo smarty_function_assign_associative(array('var'=>"params",'item'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['view']->value->element('file_item',$_smarty_tpl->tpl_vars['params']->value);?>
<table border=0 padding="0" spacing="0" style="width:100%"><tr><td colspan=2 class="vlist"><?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
</td><td colspan=2 class="vlist"><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('view/');?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</a></td><td colspan=2 class="vlist"><?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</td><td colspan=2 class="vlist"><?php echo $_smarty_tpl->tpl_vars['item']->value['mediatype'];?>
</td><td colspan=2 class="vlist"><?php echo smarty_function_math(array('equation'=>"x/y",'x'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['file_size'])===null||$tmp==='' ? 0 : $tmp),'y'=>1024,'format'=>(($tmp = @"%d")===null||$tmp==='' ? '' : $tmp)),$_smarty_tpl);?>
 KB</td><td colspan=2 class="vlist"><?php echo $_smarty_tpl->tpl_vars['item']->value['status'];?>
</td><td colspan=2 class="vlist"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['item']->value['created'],'%b %e, %Y');?>
</td><?php if ((empty($_smarty_tpl->tpl_vars['item']->value['fixed']))){?><td style="text-align:left;"><input type="checkbox" style="width:15px" name="objects_selected[]" class="objectCheck" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" /></td><?php }?><td style="text-align:right;"><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('view/');?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" class="BEbutton">+</a></td></tr></table></div><?php } ?></div>
	<br style="margin:0px; line-height:0px; clear:both" />


<?php if (!empty($_smarty_tpl->tpl_vars['objects']->value)){?>

<div style="border-top: 1px solid gray; padding-top:10px; margin-top:10px; white-space:nowrap">
	
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Go to page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changePageSelect('pagSelectBottom');?>
 
	&nbsp;&nbsp;&nbsp;
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Dimensions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changeDimSelect('selectTop');?>
 &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
(un)select all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>

	
</div>

<br />

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Bulk actions on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span class="selecteditems evidence"></span> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
selected records<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<div class="htabcontent" style="width:620px">

<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
change status to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: 	<select style="width:75px" id="newStatus" name="newStatus">
								<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['conf']->value->statusOptions),$_smarty_tpl);?>

							</select>
			<input id="changestatusSelected" type="button" value=" ok " class="opButton" />
	<hr />

	<?php if (!empty($_smarty_tpl->tpl_vars['tree']->value)){?>

		<?php $_smarty_tpl->tpl_vars['named_arr'] = new Smarty_variable($_smarty_tpl->tpl_vars['view']->value->params['named'], null, 0);?>
		<?php if (empty($_smarty_tpl->tpl_vars['named_arr']->value['id'])){?>
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

		<input type="hidden" name="data[source]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['named_arr']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
		<input id="assocObjects" type="button" value=" ok " />
		<hr />

		<?php if (!empty($_smarty_tpl->tpl_vars['named_arr']->value)){?>
		<input id="removeFromAreaSection" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove selected from section<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="opButton" />
		<hr/>
		<?php }?>
	<?php }?>

	<input id="deleteSelected" type="button" value="X <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Delete selected items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
	
</div>

<?php }?>

</form>


<?php }} ?>