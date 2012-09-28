<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:47
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/documents/inc/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:368927965504dfd988754e0-20220578%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bc076e8934831b9d2f31e84269ef248298cd99da' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/documents/inc/form.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '368927965504dfd988754e0-20220578',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd989c2956_80115102',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'view' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd989c2956_80115102')) {function content_504dfd989c2956_80115102($_smarty_tpl) {?>

<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/documents/save');?>
" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_title_subtitle');?>

	
	<?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->addshorttext)===null||$tmp==='' ? true : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_textbody',array('addshorttext'=>$_tmp1,'height'=>500));?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_file_list',array('relation'=>'attach'));?>

			
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_previews');?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_properties',array('comments'=>true));?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_tree');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_categories');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_tags');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_links');?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_geotag');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_translations');?>


	<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['conf']->value->objectTypes['document']['id'];?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_assoc_objects',array('object_type_id'=>$_tmp2));?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_advanced_properties',array('el'=>$_smarty_tpl->tpl_vars['object']->value));?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_custom_properties');?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_permissions',array('el'=>$_smarty_tpl->tpl_vars['object']->value,'recursion'=>true));?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_versions');?>


</form>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_print');?>
<?php }} ?>