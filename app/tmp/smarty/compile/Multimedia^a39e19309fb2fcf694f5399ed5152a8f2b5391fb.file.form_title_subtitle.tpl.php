<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_title_subtitle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:673014487504ef6da1f75b8-29791213%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a39e19309fb2fcf694f5399ed5152a8f2b5391fb' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_title_subtitle.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '673014487504ef6da1f75b8-29791213',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6da2657b2_39152068',
  'variables' => 
  array (
    'view' => 0,
    'object' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6da2657b2_39152068')) {function content_504ef6da2657b2_39152068($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('texteditor');?>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="title">

	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
	<br />
	<input type="text" name="data[title]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['title'], ENT_QUOTES, 'UTF-8', true));?>
" id="titleBEObject" style="width:100%" />
	<br />
	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
	<br />
	<textarea style="width:100%; margin-bottom:2px; height:30px" class="mceSimple subtitle" name="data[description]"><?php echo htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['description'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true);?>
</textarea>
	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
unique name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 (<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
url name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
):</label>
	<br />
	<input type="text" id="nicknameBEObject" name="data[nickname]" style="font-style:italic; width:100%" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['nickname'], ENT_QUOTES, 'UTF-8', true));?>
"/>


</fieldset><?php }} ?>