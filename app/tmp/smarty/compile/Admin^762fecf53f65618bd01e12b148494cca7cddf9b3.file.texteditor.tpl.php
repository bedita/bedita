<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:25
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/texteditor.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1868152716506312e9947469-82039164%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '762fecf53f65618bd01e12b148494cca7cddf9b3' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/texteditor.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1868152716506312e9947469-82039164',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'conf' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_506312e9a06d94_36911014',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e9a06d94_36911014')) {function content_506312e9a06d94_36911014($_smarty_tpl) {?><?php if (((($tmp = @$_smarty_tpl->tpl_vars['conf']->value->mce)===null||$tmp==='' ? false : $tmp))){?>
	
	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("tiny_mce/tiny_mce",false);?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("tiny_mce/tiny_mce_default_init",false);?>


<?php }elseif(((($tmp = @$_smarty_tpl->tpl_vars['conf']->value->wymeditor)===null||$tmp==='' ? false : $tmp))){?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("wymeditor/jquery.wymeditor.pack",false);?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("wymeditor/wymeditor_default_init",false);?>


<?php }elseif(((($tmp = @$_smarty_tpl->tpl_vars['conf']->value->ckeditor)===null||$tmp==='' ? false : $tmp))){?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("ckeditor/ckeditor",false);?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("ckeditor/adapters/jquery",false);?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("ckeditor/ckeditor_default_init",false);?>

	
<?php }?><?php }} ?>