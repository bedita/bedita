<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_details.tpl" */ ?>
<?php /*%%SmartyHeaderCode:112335014750534971445584-69177701%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bb2fba57f83c84f26acde6ffd47862554d593e55' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_details.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '112335014750534971445584-69177701',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'item' => 0,
    'areasList' => 0,
    'area_id' => 0,
    'mailgroup_area_id' => 0,
    'public_name' => 0,
    'mailgroup_visible' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497152c805_56613843',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497152c805_56613843')) {function content_5053497152c805_56613843($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><div class="tab"><h2>List details</h2></div>
<fieldset id="details">
	<table class="bordered">
		<tr>
			<td>
				<label for="groupname"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
list name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
			</td>
			<td>
				<input type="hidden" name="data[MailGroup][id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
				<input type="text" style="width:360px;" id="groupname" name="data[MailGroup][group_name]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['group_name'])===null||$tmp==='' ? '' : $tmp);?>
" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="publishing"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
publication<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
			</td>
			<td><?php $_smarty_tpl->tpl_vars['mailgroup_area_id'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['item']->value['area_id'])===null||$tmp==='' ? '' : $tmp), null, 0);?>
				<select style="width:220px" name="data[MailGroup][area_id]">
					<?php  $_smarty_tpl->tpl_vars["public_name"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["public_name"]->_loop = false;
 $_smarty_tpl->tpl_vars["area_id"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['areasList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["public_name"]->key => $_smarty_tpl->tpl_vars["public_name"]->value){
$_smarty_tpl->tpl_vars["public_name"]->_loop = true;
 $_smarty_tpl->tpl_vars["area_id"]->value = $_smarty_tpl->tpl_vars["public_name"]->key;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['area_id']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['area_id']->value==$_smarty_tpl->tpl_vars['mailgroup_area_id']->value){?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['public_name']->value;?>
</option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2><?php $_smarty_tpl->tpl_vars['mailgroup_visible'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['item']->value['visible'])===null||$tmp==='' ? '1' : $tmp), null, 0);?>
				<input type="radio" name="data[MailGroup][visible]" value="1" <?php if ($_smarty_tpl->tpl_vars['mailgroup_visible']->value=='1'){?>checked="true"<?php }?>/>
				<label for="visible"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
public list	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label> (people can subscribe)
			&nbsp;
				<input type="radio" name="data[MailGroup][visible]" value="0" <?php if ($_smarty_tpl->tpl_vars['mailgroup_visible']->value=='0'){?>checked="true"<?php }?>/>
				<label for="visible"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
private list <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label> (back-end insertions only)
			</td>
		</tr>
		</table>
	</fieldset><?php }} ?>