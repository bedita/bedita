<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:15
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_versions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1757372590504ef5e31a85d8-11996314%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '41ec0bc109353a612fde1d96edc95c4868ace157' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_versions.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1757372590504ef5e31a85d8-11996314',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'object' => 0,
    'h' => 0,
    'conf' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e3255922_15753910',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e3255922_15753910')) {function content_504ef5e3255922_15753910($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?><div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Versions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="history">

<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['Version'])){?>
<table class="indexlist">	
<tr>
	<th style="text-align:center; width:20px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
editor<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	<th></th>
</tr>
<?php  $_smarty_tpl->tpl_vars['h'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['h']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = array_reverse($_smarty_tpl->tpl_vars['object']->value['Version']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['h']->key => $_smarty_tpl->tpl_vars['h']->value){
$_smarty_tpl->tpl_vars['h']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['h']->key;
?>
	
	<tr>
		<td style="text-align:center">
			<?php echo $_smarty_tpl->tpl_vars['h']->value['revision'];?>

		</td>
		<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['h']->value['created'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</td>
		<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['h']->value['User']['realname'])===null||$tmp==='' ? '' : $tmp);?>
 [ <?php echo (($tmp = @$_smarty_tpl->tpl_vars['h']->value['User']['userid'])===null||$tmp==='' ? '' : $tmp);?>
 ]</td>
		<td><a class="modalbutton" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/revision');?>
/<?php echo $_smarty_tpl->tpl_vars['object']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['h']->value['revision'];?>
">  view  </a></td>
	</tr>
	
<?php } ?>
</table>
<?php }else{ ?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No versions set<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>


</fieldset>
<?php }} ?>