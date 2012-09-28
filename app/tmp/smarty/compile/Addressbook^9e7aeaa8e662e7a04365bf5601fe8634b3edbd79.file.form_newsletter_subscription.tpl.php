<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form_newsletter_subscription.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1789298570504ef5e21486c6-94750991%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9e7aeaa8e662e7a04365bf5601fe8634b3edbd79' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form_newsletter_subscription.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1789298570504ef5e21486c6-94750991',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'moduleList' => 0,
    'groupsByArea' => 0,
    'pub' => 0,
    'groups' => 0,
    'index' => 0,
    'group' => 0,
    'object' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e22a5960_99734583',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e22a5960_99734583')) {function content_504ef5e22a5960_99734583($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['moduleList']->value['newsletter'])&&$_smarty_tpl->tpl_vars['moduleList']->value['newsletter']['status']=="on"){?>
<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Newsletter subscriptions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="subscriberdetails">	
		<table class="bordered">
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
in recipient groups<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td colspan="2">
			<?php if (!empty($_smarty_tpl->tpl_vars['groupsByArea']->value)){?>
				<?php  $_smarty_tpl->tpl_vars["groups"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["groups"]->_loop = false;
 $_smarty_tpl->tpl_vars["pub"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['groupsByArea']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["groups"]->key => $_smarty_tpl->tpl_vars["groups"]->value){
$_smarty_tpl->tpl_vars["groups"]->_loop = true;
 $_smarty_tpl->tpl_vars["pub"]->value = $_smarty_tpl->tpl_vars["groups"]->key;
?>
					<ul>
					<?php echo $_smarty_tpl->tpl_vars['pub']->value;?>

					<?php  $_smarty_tpl->tpl_vars["group"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["group"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["fc"]['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars["group"]->key => $_smarty_tpl->tpl_vars["group"]->value){
$_smarty_tpl->tpl_vars["group"]->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["fc"]['index']++;
?>
					<?php $_smarty_tpl->tpl_vars["index"] = new Smarty_variable($_smarty_tpl->getVariable('smarty')->value['foreach']['fc']['index'], null, 0);?>
						<li>
						<input type="checkbox" name="data[joinGroup][<?php echo $_smarty_tpl->tpl_vars['index']->value;?>
][mail_group_id]" value="<?php echo $_smarty_tpl->tpl_vars['group']->value['id'];?>
"<?php if (!empty($_smarty_tpl->tpl_vars['group']->value['subscribed'])){?> checked<?php }?>/> <?php echo $_smarty_tpl->tpl_vars['group']->value['group_name'];?>

						<input type="hidden" name="data[joinGroup][<?php echo $_smarty_tpl->tpl_vars['index']->value;?>
][id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['group']->value['MailGroupCard']['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
						<input type="hidden" name="data[joinGroup][<?php echo $_smarty_tpl->tpl_vars['index']->value;?>
][status]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['group']->value['MailGroupCard']['status'])===null||$tmp==='' ? "confirmed" : $tmp);?>
" />
						</li>
					<?php } ?>
					
					</ul>
				<?php } ?>
			<?php }?>
			</td>
		</tr>
		<tr>
			<th><label id="lemail" for="email"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
with email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></th>
			<td colspan="2"><input type="text" id="email" name="data[newsletter_email]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['newsletter_email'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
		</tr>
		<tr>
			<th>status</th>
			<td colspan="2">
				<input type="radio" name="data[mail_status]" value="valid"<?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_status'])===null||$tmp==='' ? "valid" : $tmp)=="valid"){?> checked<?php }?> /> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
valid<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				<input type="radio" name="data[mail_status]" value="blocked"<?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_status'])===null||$tmp==='' ? "valid" : $tmp)=="blocked"){?> checked<?php }?> /> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
blocked<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
			</td>
		</tr>
		<tr>
			<th>html</th>
			<td colspan="2">
				<input type="radio" name="data[mail_html]" value="1"<?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_html'])===null||$tmp==='' ? 1 : $tmp)==1){?> checked<?php }?>> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
yes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				&nbsp;&nbsp;
				<input type="radio" name="data[mail_html]" value="0"<?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_html'])===null||$tmp==='' ? 1 : $tmp)==0){?> checked<?php }?>> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</td>
		</tr>
		</table>
</fieldset>
<?php }?>


<?php }} ?>