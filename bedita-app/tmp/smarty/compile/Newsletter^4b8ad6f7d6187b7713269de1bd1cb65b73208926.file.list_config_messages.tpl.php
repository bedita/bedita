<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_config_messages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20336802695053497180dd16-30582952%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4b8ad6f7d6187b7713269de1bd1cb65b73208926' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_config_messages.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20336802695053497180dd16-30582952',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'item' => 0,
    'mailgroup_opting_method' => 0,
    'default_confirmation_in_message' => 0,
    'default_confirmation_out_message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_505349718c6427_70154193',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_505349718c6427_70154193')) {function content_505349718c6427_70154193($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><div class="tab"><h2>Config and messages</h2></div>
<fieldset id="configmessages">		
	<table class="bordered">
		<tr>
			<td colspan="2"><?php $_smarty_tpl->tpl_vars['mailgroup_opting_method'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['item']->value['security'])===null||$tmp==='' ? '' : $tmp), null, 0);?>
				<label for="optingmethod"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
subscribing method<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
				&nbsp;&nbsp;
				<select id="optingmethod" name="data[MailGroup][security]">
					<option value="none"<?php if ($_smarty_tpl->tpl_vars['mailgroup_opting_method']->value=='none'){?> selected<?php }?>>Single opt-in (no confirmation required)</option>
					<option value="all"<?php if ($_smarty_tpl->tpl_vars['mailgroup_opting_method']->value=='all'){?> selected<?php }?>>Double opt-in (confirmation required)</option>
				</select>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top">
				<label for="confirmin"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Confirmation-In mail message<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_in_message]" id="optinmessage" style="width:220px" class="autogrowarea"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['confirmation_in_message'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['default_confirmation_in_message']->value : $tmp);?>
</textarea>
			</td>
			<td style="vertical-align:top">
				<label for="confirmout"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Confirmation-Out mail message<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_out_message]" id="optoutmessage" style="width:220px" class="autogrowarea"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['confirmation_out_message'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['default_confirmation_out_message']->value : $tmp);?>
</textarea>
			</td>
		</tr>
	</table>
</fieldset>
<?php }} ?>