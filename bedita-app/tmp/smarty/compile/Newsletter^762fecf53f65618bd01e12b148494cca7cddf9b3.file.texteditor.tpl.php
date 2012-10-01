<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:13:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/texteditor.tpl" */ ?>
<?php /*%%SmartyHeaderCode:604834345053497e1c33c4-04027740%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '762fecf53f65618bd01e12b148494cca7cddf9b3' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/texteditor.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '604834345053497e1c33c4-04027740',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'conf' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497e2d7661_25921265',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497e2d7661_25921265')) {function content_5053497e2d7661_25921265($_smarty_tpl) {?><?php if (((($tmp = @$_smarty_tpl->tpl_vars['conf']->value->mce)===null||$tmp==='' ? false : $tmp))){?>
	
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