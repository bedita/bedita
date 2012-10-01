<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:05
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:872105938504ef5d9b83eb2-65194403%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fcd38cc8c615c19e6bb635d8baa600adf6b12903' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/index.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '872105938504ef5d9b83eb2-65194403',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'view' => 0,
    'params' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5d9c08c56_33718450',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5d9c08c56_33718450')) {function content_504ef5d9c08c56_33718450($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?>
<?php echo $_smarty_tpl->tpl_vars['view']->value->set('method',$_smarty_tpl->tpl_vars['view']->value->action);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo smarty_function_assign_associative(array('var'=>"params",'itemName'=>"cards"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('toolbar',$_smarty_tpl->tpl_vars['params']->value);?>




<div class="mainfull">

	<?php echo $_smarty_tpl->getSubTemplate ("inc/list_objects.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	

</div>

<?php }} ?>