<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:47:53
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_link_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1746042532504f16d9473b46-32595950%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5ed7b0fdf312daabd01382eaf8e2451bf1b1fd1' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_link_item.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1746042532504f16d9473b46-32595950',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'objRelated' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f16d953c047_22402570',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f16d953c047_22402570')) {function content_504f16d953c047_22402570($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><td style="padding:0px !important">

	<input type="hidden" class="id"  name="data[RelatedObject][link][<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['id'];?>
][id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
	<input type="text" name="data[RelatedObject][link][<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['id'];?>
][priority]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['priority'])===null||$tmp==='' ? 1 : $tmp);?>
" size="3" maxlength="3" class="priority" style="width:20px; padding:0px; margin:0px !important;" />
</td>
<td>
	<input type="text" class="linkcontent" style="width:140px" name="data[RelatedObject][link][<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['id'];?>
][title]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['title'])===null||$tmp==='' ? '' : $tmp);?>
" />
</td>
<td>
	<input type="text" class="linkcontent" style="width:230px" value="<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['url'];?>
" name="data[RelatedObject][link][<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['id'];?>
][url]" />
</td>


<td style="white-space:nowrap">
	<input type="button" class="BEbutton golink" onClick="window.open($(this).attr('href'));" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
webmarks/view/<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['id'];?>
" name="details" value="››" />&nbsp;
	<input type="button" class="remove" title="remove" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
X<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />
	&nbsp; <a href="<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['url'];?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
open in new window<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" target="_blank">open</a>
	
		
	<input type="hidden" class="linkmod" name="data[RelatedObject][link][<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['id'];?>
][modified]" value="1" />
	
</td>
<?php }} ?>