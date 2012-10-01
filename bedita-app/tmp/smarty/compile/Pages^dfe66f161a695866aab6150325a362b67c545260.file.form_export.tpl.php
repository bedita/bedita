<?php /* Smarty version Smarty-3.1.11, created on 2012-09-17 12:20:48
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/pages/form_export.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9805033965056f9807fca43-75304202%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dfe66f161a695866aab6150325a362b67c545260' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/pages/form_export.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9805033965056f9807fca43-75304202',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'objectId' => 0,
    'conf' => 0,
    'filter' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5056f980894b11_77162452',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5056f980894b11_77162452')) {function content_5056f980894b11_77162452($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/areas/export');?>
" method="post" name="exportForm" id="exportForm">

<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objectId']->value)===null||$tmp==='' ? '' : $tmp);?>
"/>

<fieldset id="export" style="padding:20px">

	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
export<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 object <?php echo (($tmp = @$_smarty_tpl->tpl_vars['objectId']->value)===null||$tmp==='' ? '' : $tmp);?>
</label>
	
	&nbsp;&nbsp;&nbsp; <input type="checkbox" checked=1 /> recursive (include all children)
	&nbsp;&nbsp;&nbsp; <input type="checkbox" /> verbose (include all attributes)
	<hr />
	
	<div>
		<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_smarty_tpl->tpl_vars['filter'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->filters['export']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value){
$_smarty_tpl->tpl_vars['val']->_loop = true;
 $_smarty_tpl->tpl_vars['filter']->value = $_smarty_tpl->tpl_vars['val']->key;
?>
			<input name="data[type]" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value;?>
" /><?php echo $_smarty_tpl->tpl_vars['filter']->value;?>
 &nbsp;
		<?php } ?>
		<hr />
		<input type="checkbox" /> include media files
		&nbsp;&nbsp;&nbsp;
		<input type="checkbox" /> compress output
	</div>

	<hr />
		
		filename: <input type="text" name="data[filename]" value="bedita_export_<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objectId']->value)===null||$tmp==='' ? '' : $tmp);?>
">
	
	<hr />
	<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
export<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />

</fieldset>

</form><?php }} ?>