<?php /* Smarty version Smarty-3.1.11, created on 2012-09-10 18:08:28
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/streams/show_streams.tpl" */ ?>
<?php /*%%SmartyHeaderCode:56684782504e107c29ada4-19893292%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ddb1663e1491210efce6d2ae3cfdadeee9597452' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/streams/show_streams.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '56684782504e107c29ada4-19893292',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'bedita_items' => 0,
    'params' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504e107c345786_19593680',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e107c345786_19593680')) {function content_504e107c345786_19593680($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.tablesorter.min");?>


<?php echo smarty_function_assign_associative(array('var'=>"params",'itemType'=>"attachments",'items'=>$_smarty_tpl->tpl_vars['bedita_items']->value,'relation'=>'attach'),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_multimedia_assoc',$_smarty_tpl->tpl_vars['params']->value);?>
<?php }} ?>