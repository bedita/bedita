<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:42
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/documents/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1649945883504e09aba68de7-26838662%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7259d269e6e2d75e01ea11b82ee0bf47c703fff5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/documents/index.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1649945883504e09aba68de7-26838662',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e09abb8a227_95405194',
  'variables' => 
  array (
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e09abb8a227_95405194')) {function content_504e09abb8a227_95405194($_smarty_tpl) {?>
<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('toolbar');?>


<div class="mainfull">

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('list_objects');?>

	
</div><?php }} ?>