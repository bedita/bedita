<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:25
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/admin/system_info.tpl" */ ?>
<?php /*%%SmartyHeaderCode:796458588506312e96bd5b1-50279320%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12a27faeea37347b74722d00ce3bf2bcf474ced8' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/admin/system_info.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '796458588506312e96bd5b1-50279320',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_506312e97b69d0_91047855',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e97b69d0_91047855')) {function content_506312e97b69d0_91047855($_smarty_tpl) {?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.treeview",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("form",false);?>



<script type="text/javascript">
	$(document).ready(function(){
		var openAtStart ="#system_info";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"systemInfo"), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"systemInfo",'fixed'=>true), 0);?>


<div class="mainfull">
	
	<?php echo $_smarty_tpl->getSubTemplate ("inc/form_info.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"systemInfo"), 0);?>


</div>
<?php }} ?>