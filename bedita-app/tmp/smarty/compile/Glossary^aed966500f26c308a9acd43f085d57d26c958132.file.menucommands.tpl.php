<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:37:15
         compiled from "/home/bato/workspace/bedita-plugins/glossary/views/elements/menucommands.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1566935773504f145b141b34-88282254%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aed966500f26c308a9acd43f085d57d26c958132' => 
    array (
      0 => '/home/bato/workspace/bedita-plugins/glossary/views/elements/menucommands.tpl',
      1 => 1327664703,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1566935773504f145b141b34-88282254',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'method' => 0,
    'view' => 0,
    'fixed' => 0,
    'session' => 0,
    'html' => 0,
    'currentModule' => 0,
    'moduleName' => 0,
    'back' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f145b26c1b8_91278049',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f145b26c1b8_91278049')) {function content_504f145b26c1b8_91278049($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->set("method",$_smarty_tpl->tpl_vars['method']->value);?>

<div class="secondacolonna <?php if (!empty($_smarty_tpl->tpl_vars['fixed']->value)){?>fixed<?php }?>">
	
	<?php if (!empty($_smarty_tpl->tpl_vars['method']->value)&&$_smarty_tpl->tpl_vars['method']->value!="index"){?>
		<?php $_smarty_tpl->tpl_vars["back"] = new Smarty_variable($_smarty_tpl->tpl_vars['session']->value->read("backFromView"), null, 0);?>
	<?php }else{ ?>
		<?php echo smarty_function_assign_concat(array('var'=>"back",1=>$_smarty_tpl->tpl_vars['html']->value->url('/'),2=>$_smarty_tpl->tpl_vars['currentModule']->value['url']),$_smarty_tpl);?>

	<?php }?>

	<div class="modules">
		<label class="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
" rel="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div> 
	
	
	<?php if (!empty($_smarty_tpl->tpl_vars['method']->value)&&$_smarty_tpl->tpl_vars['method']->value!="index"){?>
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
clone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" name="delete" id="delBEObject" />
	</div>
	
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element("prevnext");?>

	
	<?php }?>

	<?php if (!empty($_smarty_tpl->tpl_vars['view']->value->action)&&$_smarty_tpl->tpl_vars['view']->value->action=="index"){?>
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('select_categories');?>

	<?php }?>

</div>

<?php }} ?>