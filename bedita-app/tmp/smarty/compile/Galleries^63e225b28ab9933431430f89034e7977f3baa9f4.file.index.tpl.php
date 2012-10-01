<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:38:17
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/galleries/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1402607931504dff5f72ef59-24587686%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '63e225b28ab9933431430f89034e7977f3baa9f4' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/galleries/index.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1402607931504dff5f72ef59-24587686',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dff5f8971d2_15299984',
  'variables' => 
  array (
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dff5f8971d2_15299984')) {function content_504dff5f8971d2_15299984($_smarty_tpl) {?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('toolbar');?>




<div class="mainfull">

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('list_objects');?>

	
</div>


<?php }} ?>