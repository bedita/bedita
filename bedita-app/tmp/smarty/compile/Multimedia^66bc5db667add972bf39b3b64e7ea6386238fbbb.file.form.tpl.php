<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1374097613504ef6d9d69aa4-02054577%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '66bc5db667add972bf39b3b64e7ea6386238fbbb' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/form.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1374097613504ef6d9d69aa4-02054577',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6d9e9aed7_22799635',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'params' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6d9e9aed7_22799635')) {function content_504ef6d9e9aed7_22799635($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?>

<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/multimedia/saveAjax');?>
" enctype="multipart/form-data" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
<input  type="hidden" name="data[object_type_id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['object_type_id'])===null||$tmp==='' ? '' : $tmp);?>
" />
<input  type="hidden" name="data[uri]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['uri'];?>
" />
<input  type="hidden" name="data[name]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['name'];?>
" />
<input  type="hidden" name="data[mime_type]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['mime_type'];?>
" />

<?php echo smarty_function_assign_associative(array('var'=>"params",'publication'=>false,'comments'=>true),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_properties',$_smarty_tpl->tpl_vars['params']->value);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_title_subtitle');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_tree');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/form_mediatype.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php if ($_smarty_tpl->tpl_vars['object']->value['Category']=="spreadsheet"||$_smarty_tpl->tpl_vars['object']->value['Category']=="text"){?>
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_textbody');?>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_file');?>


<?php if (!empty($_smarty_tpl->tpl_vars['object']->value)){?>
	<?php echo $_smarty_tpl->getSubTemplate ("inc/list_relationships.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_tags');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_geotag');?>

	
<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_translations');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_advanced_properties');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_custom_properties');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_file_exif');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_notes');?>



</form>
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_versions');?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_print');?>
<?php }} ?>