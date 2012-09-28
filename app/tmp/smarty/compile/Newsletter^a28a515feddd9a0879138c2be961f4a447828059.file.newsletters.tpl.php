<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:59
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/newsletters.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21391192825053497b36f0a8-84893991%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a28a515feddd9a0879138c2be961f4a447828059' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/newsletters.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21391192825053497b36f0a8-84893991',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497b3f88a4_66693738',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497b3f88a4_66693738')) {function content_5053497b3f88a4_66693738($_smarty_tpl) {?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"newsletters"), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"newsletters",'fixed'=>true), 0);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('toolbar');?>


<div class="mainfull">

	<?php echo $_smarty_tpl->getSubTemplate ("inc/list_objects_newsletter.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"index"), 0);?>

	
</div><?php }} ?>