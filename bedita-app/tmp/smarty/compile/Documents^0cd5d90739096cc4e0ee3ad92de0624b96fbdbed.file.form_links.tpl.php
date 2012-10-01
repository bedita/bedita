<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:48
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_links.tpl" */ ?>
<?php /*%%SmartyHeaderCode:971526132504dfd9b9cdd84-47290864%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0cd5d90739096cc4e0ee3ad92de0624b96fbdbed' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_links.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '971526132504dfd9b9cdd84-47290864',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9bad5ae7_91189638',
  'variables' => 
  array (
    'html' => 0,
    'relObjects' => 0,
    'objRelated' => 0,
    'params' => 0,
    'view' => 0,
    'prior' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9bad5ae7_91189638')) {function content_504dfd9bad5ae7_91189638($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><script type="text/javascript">
<!--

var urlBaseAddLink = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/addLink');?>
";

function addItem() {
	
	var divToFill = "#listExistingLinks";
	$("#loadingLinks").show();
	var emptyLI = "<tr><\/tr>"; 
	var linkTitle=$("#linkTitle").val();
	var linkUrl=$("#linkUrl").val();
	var target=$("#linkTarget").val();
	
	$(emptyLI).load(urlBaseAddLink, { 'title': linkTitle, 'url':linkUrl, 'target':target }, function () {
		
		$("#listExistingLinks").append(this).fixItemsPriority() ; 
	
		$("#loadingLinks").hide();
		$(this).find("input[type='button']").click(function() {
			
			$(this).parents("tr").remove();
			$("#listExistingLinks").fixItemsPriority();
			
		});
	}) ;
}


$(document).ready(function() {
	$("#addLink").click(function () {
		addItem();
		$(".new").val('');
	});
	
	$("#listExistingLinks .remove").click(function() {
		
		$(this).parents("tr").remove();
		$("#listExistingLinks").fixItemsPriority();
	
	});
	
	$("#listExistingLinks").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority
	});
	
});

//-->
</script>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Links<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="links">
	
	<input type="hidden" name="data[RelatedObject][link][0][switch]" value="link" />


	<table border="0" class="condensed" style="margin-left:-5px; margin-top:-10px;">
		<thead>
			<tr>
				<th></th><th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th><th>url</th>
			</tr>
		</thead>
		<tbody id="listExistingLinks">
			<?php if (isset($_smarty_tpl->tpl_vars['relObjects']->value['link'])){?>
			
				<?php  $_smarty_tpl->tpl_vars["objRelated"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["objRelated"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['relObjects']->value['link']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars["objRelated"]->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["linkForeach"]['total'] = $_smarty_tpl->tpl_vars["objRelated"]->total;
foreach ($_from as $_smarty_tpl->tpl_vars["objRelated"]->key => $_smarty_tpl->tpl_vars["objRelated"]->value){
$_smarty_tpl->tpl_vars["objRelated"]->_loop = true;
?>
					<?php echo smarty_function_assign_associative(array('var'=>"params",'objRelated'=>$_smarty_tpl->tpl_vars['objRelated']->value),$_smarty_tpl);?>

					<tr><?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_link_item',$_smarty_tpl->tpl_vars['params']->value);?>
</tr>
				<?php } ?>
			
			<?php }?>
		</tbody>
	
	<?php $_smarty_tpl->tpl_vars["prior"] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('smarty')->value['foreach']['linkForeach']['total'])===null||$tmp==='' ? 0 : $tmp), null, 0);?>
		<tfoot>
			<tr id="loadingLinks" style="display:none">
				<td></td><td colspan="3">loading...</td>
			</tr>
			<tr id="newLink">
				<td style="padding:0px !important"><input type="text" class="priority" 
				style="width:20px; padding:0px; margin:0px !important;" name="linkPriority" value="<?php echo $_smarty_tpl->tpl_vars['prior']->value+1;?>
" size="3" maxlength="3"/></td>
				<td><input type="text" class="new" style="width:140px" name="linkTitle" id="linkTitle" /></td>
				<td><input type="text" class="new" style="width:230px" name="linkUrl" id="linkUrl" /></td>
				<td><input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" id="addLink"/></td>
		
			</tr>
		</tfoot>	
	</table>
	
</fieldset>
<?php }} ?>