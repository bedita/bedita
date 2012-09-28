<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:13:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_message_details.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11042475915053497e31fe87-67938201%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '62a5555bc57c44290a15a0f1598f142727c81b36' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_message_details.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11042475915053497e31fe87-67938201',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'object' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497e3cd633_57242517',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497e3cd633_57242517')) {function content_5053497e3cd633_57242517($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><table class="bordered" style="width:100%" id="">
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sender name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td><input type="text" name="data[sender_name]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['sender_name'])===null||$tmp==='' ? null : $tmp);?>
" /></td>
	</tr>
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sender email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td><input type="text" name="data[sender]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['sender'])===null||$tmp==='' ? null : $tmp);?>
" class="required email" /></td>
	</tr>
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reply to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td><input type="text" name="data[reply_to]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['reply_to'])===null||$tmp==='' ? null : $tmp);?>
" class="email"/></td>
	</tr>
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
bounce to email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td><input type="text" name="data[bounce_to]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['bounce_to'])===null||$tmp==='' ? null : $tmp);?>
" class="email"/></td>
	</tr>
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
priority<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td><input type="text" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['priority'])===null||$tmp==='' ? null : $tmp);?>
" /></td>
	</tr>
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
signature<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
		<td>	
			<textarea name="data[signature]" style="width:340px" class="autogrowarea"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['signature'])===null||$tmp==='' ? null : $tmp);?>
</textarea>
		</td>
	</tr>
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
privacy disclaimer<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
		<td>	
			<textarea name="data[privacy_disclaimer]" style="width:340px" class="autogrowarea"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['privacy_disclaimer'])===null||$tmp==='' ? null : $tmp);?>
</textarea>
		</td>
	</tr>
</table><?php }} ?>