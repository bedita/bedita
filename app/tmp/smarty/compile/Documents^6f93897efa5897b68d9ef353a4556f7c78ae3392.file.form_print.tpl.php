<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_print.tpl" */ ?>
<?php /*%%SmartyHeaderCode:782769969504dfd9c97eb33-51743697%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6f93897efa5897b68d9ef353a4556f7c78ae3392' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_print.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '782769969504dfd9c97eb33-51743697',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9ca0ec65_70397014',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'tree' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9ca0ec65_70397014')) {function content_504dfd9ca0ec65_70397014($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>	
<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/printme');?>
" target="print" method="post">
<input type="hidden" name="id" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Print<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset class="whitebox" id="print">

	<table border=0>
		<tr>
			<th><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Print layout<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label></th>
			<td><?php echo $_smarty_tpl->tpl_vars['html']->value->image('/img/print_tab_1fold.png');?>
</td>
			<td><input type="radio" name="printLayout" checked='checked' value="print_1col"> <label>1col</label></td>
			<td><?php echo $_smarty_tpl->tpl_vars['html']->value->image('/img/print_tab_2fold.png');?>
</td>
			<td><input type="radio" name="printLayout" value="print_2col"> <label>2col</label></td>
			<td><?php echo $_smarty_tpl->tpl_vars['html']->value->image('/img/print_tab_3fold.png');?>
</td>
			<td><input type="radio" name="printLayout" value="print_3col"> <label>3col</label></td>
		</tr>
	</table>
	<hr />
	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
print context<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:&nbsp;</label>
	<select name="printcontext">
		<option>BEdita standard report</option>
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tree']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</option>
	<?php } ?>	
	</select>
	
	&nbsp;&nbsp;&nbsp;<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
print<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">

</fieldset>

</form><?php }} ?>