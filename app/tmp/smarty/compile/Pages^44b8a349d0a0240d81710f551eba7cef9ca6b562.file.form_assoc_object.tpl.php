<?php /* Smarty version Smarty-3.1.11, created on 2012-09-10 18:06:46
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_assoc_object.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1263870823504e1016d5fe55-41370045%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '44b8a349d0a0240d81710f551eba7cef9ca6b562' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_assoc_object.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1263870823504e1016d5fe55-41370045',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'objsRelated' => 0,
    'objRelated' => 0,
    'rel' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504e101717ad77_52003083',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e101717ad77_52003083')) {function content_504e101717ad77_52003083($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
if (!is_callable('smarty_modifier_filesize')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/modifier.filesize.php';
?><?php  $_smarty_tpl->tpl_vars["objRelated"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["objRelated"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['objsRelated']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["objRelated"]->key => $_smarty_tpl->tpl_vars["objRelated"]->value){
$_smarty_tpl->tpl_vars["objRelated"]->_loop = true;
?>
<tr class="obj <?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['status'])===null||$tmp==='' ? '' : $tmp);?>
">
	<td style="padding:0px; width:20px;">
	<input type="hidden" class="rel_nickname" value="<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['nickname'];?>
">
		<input type="hidden" class="id" name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
][<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
][id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
		<input type="text" class="priority" 
				style="margin:0px; width:20px; text-align:right; background-color:transparent"
				name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
][<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
][priority]" 
				value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['priority'])===null||$tmp==='' ? '' : $tmp);?>
" size="3" maxlength="3"/>
	</td>

	<td style="width:10px;">
		<span title="<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['ObjectType']['name'];?>
" class="listrecent <?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['ObjectType']['module_name'])===null||$tmp==='' ? '' : $tmp);?>
" style="margin:0px">&nbsp;</span>
	</td>
	
	<td>
		<?php echo smarty_modifier_truncate((($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp),60,'~',true);?>

	</td>

<?php if ($_smarty_tpl->tpl_vars['rel']->value=="download"){?>

	<td><?php echo smarty_modifier_truncate((($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['mime_type'])===null||$tmp==='' ? '' : $tmp),60,'~',true);?>
</td>
	
	<td style="text-align:right"><?php echo smarty_modifier_filesize((($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['file_size'])===null||$tmp==='' ? 0 : $tmp));?>
</td>

<?php }?>

	<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['status'])===null||$tmp==='' ? '' : $tmp);?>
</td>
	
	<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['lang'])===null||$tmp==='' ? '' : $tmp);?>
</td>
	
	<td style="text-align:right; white-space:nowrap">
		<input class="BEbutton golink" 
		title="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['title'])===null||$tmp==='' ? '[no title]' : $tmp);?>
, <?php echo (($tmp = @$_smarty_tpl->tpl_vars['objRelated']->value['mime_type'])===null||$tmp==='' ? '' : $tmp);?>
" 
		rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['ObjectType']['module_name'];?>
/view/<?php echo $_smarty_tpl->tpl_vars['objRelated']->value['id'];?>
" name="details" type="button" value="details">
		<input class="BEbutton" name="remove" type="button" value="x">
	</td>

</tr>
<?php } ?><?php }} ?>