<?php /* Smarty version Smarty-3.1.11, created on 2012-09-19 15:31:29
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/form_section.tpl" */ ?>
<?php /*%%SmartyHeaderCode:85110404150535c6841c8d9-44657111%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '21eedd83cb739150e6fff0654fcae02ec29495e1' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/form_section.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '85110404150535c6841c8d9-44657111',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50535c6855ead8_23736549',
  'variables' => 
  array (
    'object' => 0,
    'params' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c6855ead8_23736549')) {function content_50535c6855ead8_23736549($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?>
	<?php echo $_smarty_tpl->getSubTemplate ("inc/form_properties.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('fixed'=>false), 0);?>

	
	<?php echo smarty_function_assign_associative(array('var'=>"params",'object'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? null : $tmp)),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_tags',$_smarty_tpl->tpl_vars['params']->value);?>


	<?php echo smarty_function_assign_associative(array('var'=>"params",'object'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? null : $tmp)),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_categories');?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_geotag');?>


	<?php echo smarty_function_assign_associative(array('var'=>"params",'object'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? null : $tmp)),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_translations',$_smarty_tpl->tpl_vars['params']->value);?>


	<?php echo smarty_function_assign_associative(array('var'=>"params",'object'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? null : $tmp)),$_smarty_tpl);?>
	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_advanced_properties');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_custom_properties');?>

	
	<?php echo smarty_function_assign_associative(array('var'=>"params",'el'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? null : $tmp),'recursion'=>true),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_permissions',$_smarty_tpl->tpl_vars['params']->value);?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_versions');?>


<?php }} ?>