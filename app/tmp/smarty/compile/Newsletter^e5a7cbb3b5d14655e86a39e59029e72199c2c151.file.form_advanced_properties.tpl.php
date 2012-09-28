<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:13:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_advanced_properties.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16782243395053497e5a8ae1-99378449%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5a7cbb3b5d14655e86a39e59029e72199c2c151' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_advanced_properties.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16782243395053497e5a8ae1-99378449',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'object' => 0,
    'alias' => 0,
    'conf' => 0,
    'code' => 0,
    'lic' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497e85e898_97503085',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497e85e898_97503085')) {function content_5053497e85e898_97503085($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Advanced Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="advancedproperties" class="multimediaiteminside">

<table class="bordered">
<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['object']->value['id'];?>

		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
unique name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			
			<?php echo $_smarty_tpl->tpl_vars['object']->value['nickname'];?>

		</td>
	</tr>

	<?php if (($_smarty_tpl->tpl_vars['object']->value)){?>
		
		<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['Alias'])){?>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Alias<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<ul>
				<?php  $_smarty_tpl->tpl_vars['alias'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['alias']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['object']->value['Alias']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['alias']->key => $_smarty_tpl->tpl_vars['alias']->value){
$_smarty_tpl->tpl_vars['alias']->_loop = true;
?>
					<?php echo $_smarty_tpl->tpl_vars['alias']->value['nickname_alias'];?>

				<?php } ?>
				</ul>
			</td>
		</tr>
		<?php }?>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
created by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['UserCreated']['realname'])===null||$tmp==='' ? '' : $tmp);?>
 [ <?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['UserCreated']['userid'])===null||$tmp==='' ? '' : $tmp);?>
 ]</td>
		</tr>	
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
created on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['created'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</td>
		</tr>	 
		<tr>
			<th style="white-space:nowrap"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last modified on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</td>
		</tr>
		<tr>
			<th style="white-space:nowrap"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last modified by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['UserModified']['realname'])===null||$tmp==='' ? '' : $tmp);?>
 [ <?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['UserModified']['userid'])===null||$tmp==='' ? '' : $tmp);?>
 ]</td>
		</tr>
		
	<?php }?>

	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
publisher<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[publisher]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['publisher'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
	</tr>
	<tr>
		<th>&copy; <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
rights<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[rights]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['rights'])===null||$tmp==='' ? '' : $tmp);?>
" /></td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
license<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<select style="width:300px;" name="data[license]">
				<option value="">--</option>
				<?php  $_smarty_tpl->tpl_vars['lic'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lic']->_loop = false;
 $_smarty_tpl->tpl_vars['code'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->defaultLicenses; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lic']->key => $_smarty_tpl->tpl_vars['lic']->value){
$_smarty_tpl->tpl_vars['lic']->_loop = true;
 $_smarty_tpl->tpl_vars['code']->value = $_smarty_tpl->tpl_vars['lic']->key;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['object']->value['license']==$_smarty_tpl->tpl_vars['code']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lic']->value['title'];?>
</option>
				<?php } ?>
				<?php  $_smarty_tpl->tpl_vars['lic'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lic']->_loop = false;
 $_smarty_tpl->tpl_vars['code'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->cfgLicenses; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lic']->key => $_smarty_tpl->tpl_vars['lic']->value){
$_smarty_tpl->tpl_vars['lic']->_loop = true;
 $_smarty_tpl->tpl_vars['code']->value = $_smarty_tpl->tpl_vars['lic']->key;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['object']->value['license']==$_smarty_tpl->tpl_vars['code']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lic']->value['title'];?>
</option>
				<?php } ?>
			</select>
		</td>
	</tr>
</table>

</fieldset>
<?php }} ?>