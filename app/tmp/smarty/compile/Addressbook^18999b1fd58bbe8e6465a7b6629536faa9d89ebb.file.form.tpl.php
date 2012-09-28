<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:13
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:221828682504ef5e1961571-97152097%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '18999b1fd58bbe8e6465a7b6629536faa9d89ebb' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form.tpl',
      1 => 1347350555,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '221828682504ef5e1961571-97152097',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'view' => 0,
    'conf' => 0,
    'params' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e1a2ab40_02611590',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e1a2ab40_02611590')) {function content_504ef5e1a2ab40_02611590($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?>


<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/addressbook/save');?>
" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>
	
	<?php echo $_smarty_tpl->getSubTemplate ("../inc/form_card_details.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


	<?php echo $_smarty_tpl->getSubTemplate ("../inc/form_properties.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
	<?php echo $_smarty_tpl->getSubTemplate ("../inc/form_newsletter_subscription.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_categories');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_tree');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_file_list',array('relation'=>'attach'));?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_tags');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_geotag');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_translations');?>

	
	<?php echo smarty_function_assign_associative(array('var'=>"params",'object_type_id'=>$_smarty_tpl->tpl_vars['conf']->value->objectTypes['card']['id']),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_assoc_objects',$_smarty_tpl->tpl_vars['params']->value);?>

	
	<?php echo $_smarty_tpl->getSubTemplate ("../inc/form_advanced_properties.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('el'=>$_smarty_tpl->tpl_vars['object']->value), 0);?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_custom_properties');?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_versions');?>


</form>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_print');?>
<?php }} ?>