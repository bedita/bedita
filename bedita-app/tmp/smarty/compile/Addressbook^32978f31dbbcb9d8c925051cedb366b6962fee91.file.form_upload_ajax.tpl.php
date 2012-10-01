<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_upload_ajax.tpl" */ ?>
<?php /*%%SmartyHeaderCode:905635166504ef5e2749706-73076946%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32978f31dbbcb9d8c925051cedb366b6962fee91' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_upload_ajax.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '905635166504ef5e2749706-73076946',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uploadIdSuffix' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e27b98a3_95883404',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e27b98a3_95883404')) {function content_504ef5e27b98a3_95883404($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<div id="ajaxUploadContainer<?php echo (($tmp = @$_smarty_tpl->tpl_vars['uploadIdSuffix']->value)===null||$tmp==='' ? '' : $tmp);?>
" style="display:none; padding:20px 0px 0px 20px;">
	
	
	<table style="margin-bottom:20px">
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
file<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
		<td><input style="width:270px;" type="file" name="Filedata<?php echo (($tmp = @$_smarty_tpl->tpl_vars['uploadIdSuffix']->value)===null||$tmp==='' ? '' : $tmp);?>
" /></td>
	</tr>
	
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
		<td><input style="width:270px;" type="text" name="streamUploaded<?php echo (($tmp = @$_smarty_tpl->tpl_vars['uploadIdSuffix']->value)===null||$tmp==='' ? '' : $tmp);?>
[title]" class="formtitolo" value=""></td>
	</tr>
	
	<tr>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
		<td><textarea name="streamUploaded<?php echo (($tmp = @$_smarty_tpl->tpl_vars['uploadIdSuffix']->value)===null||$tmp==='' ? '' : $tmp);?>
[description]" class="autogrowarea" style="width:270px; min-height:16px; height:16px;"></textarea></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="button" style="width:160px; margin-top:15px" id="uploadForm<?php echo (($tmp = @$_smarty_tpl->tpl_vars['uploadIdSuffix']->value)===null||$tmp==='' ? '' : $tmp);?>
" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/></td>
	</tr>
	</table>

	<a href="javascript:void(0);"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Use the multiple upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>

	<div id="msgUpload<?php echo (($tmp = @$_smarty_tpl->tpl_vars['uploadIdSuffix']->value)===null||$tmp==='' ? '' : $tmp);?>
"></div>

</div><?php }} ?>