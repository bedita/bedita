<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:13:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9872185175053497ddc5261-68386687%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ce6c2cdb5ab638efa25b2d5271e76757783812f0' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9872185175053497ddc5261-68386687',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'params' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497de764d9_62469774',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497de764d9_62469774')) {function content_5053497de764d9_62469774($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?>

<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/save');?>
" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>
	
	
	<?php echo $_smarty_tpl->getSubTemplate ("inc/form_contents_newsletter.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


	<?php echo $_smarty_tpl->getSubTemplate ("inc/form_invoice.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
	<?php echo smarty_function_assign_associative(array('var'=>"params",'el'=>$_smarty_tpl->tpl_vars['object']->value),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_advanced_properties',$_smarty_tpl->tpl_vars['params']->value);?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_versions');?>

</form>

<?php }} ?>