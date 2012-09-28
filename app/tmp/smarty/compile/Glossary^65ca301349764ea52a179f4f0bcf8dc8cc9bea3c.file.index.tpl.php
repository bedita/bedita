<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:37:14
         compiled from "/home/bato/workspace/bedita-plugins/glossary/views/glossary/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:40753792504f145a6d9340-59468522%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '65ca301349764ea52a179f4f0bcf8dc8cc9bea3c' => 
    array (
      0 => '/home/bato/workspace/bedita-plugins/glossary/views/glossary/index.tpl',
      1 => 1287751425,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '40753792504f145a6d9340-59468522',
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
  'unifunc' => 'content_504f145a7ad793_47113967',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f145a7ad793_47113967')) {function content_504f145a7ad793_47113967($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><?php echo $_smarty_tpl->tpl_vars['view']->value->element("modulesmenu");?>


<?php echo smarty_function_assign_associative(array('var'=>"params",'method'=>"index"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element("menuleft",$_smarty_tpl->tpl_vars['params']->value);?>


<?php echo smarty_function_assign_associative(array('var'=>"params",'method'=>"index"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element("menucommands",$_smarty_tpl->tpl_vars['params']->value);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element("toolbar");?>




<div class="mainfull">

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element("list_objects");?>

	

</div>

<?php }} ?>