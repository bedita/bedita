<?php /* Smarty version Smarty-3.1.11, created on 2012-09-17 10:18:42
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/single_note.tpl" */ ?>
<?php /*%%SmartyHeaderCode:527207295056dce2930069-87931388%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '730502ccfbba08e24f6d8c716ea362acdf4240cf' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/single_note.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '527207295056dce2930069-87931388',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'note' => 0,
    'conf' => 0,
    'BEAuthUser' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5056dce29d6ec2_83974841',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5056dce29d6ec2_83974841')) {function content_5056dce29d6ec2_83974841($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><div>
<table class="editorheader ultracondensed" style="width:100%">
<tr>
	<td class="author"><?php echo (($tmp = @(($tmp = @(($tmp = @$_smarty_tpl->tpl_vars['note']->value['UserCreated']['realname'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['note']->value['UserCreated']['userid'] : $tmp))===null||$tmp==='' ? $_smarty_tpl->tpl_vars['note']->value['creator'] : $tmp))===null||$tmp==='' ? $_smarty_tpl->tpl_vars['note']->value['user_created'] : $tmp);?>
</td>
	<td class="date"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['note']->value['created'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</td>
</tr>
</table>
<p class="editornotes"><?php echo nl2br($_smarty_tpl->tpl_vars['note']->value['description']);?>
</p>
<?php if ($_smarty_tpl->tpl_vars['note']->value['user_created']==$_smarty_tpl->tpl_vars['BEAuthUser']->value['id']){?>
	<input type="button" rel="<?php echo $_smarty_tpl->tpl_vars['note']->value['id'];?>
" 
	style="font-size:9px !important; text-transform:lowercase; margin:0px 0px 0px 120px;" 
	name="deletenote" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />
<?php }?>
</div><?php }} ?>