<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:21
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/admin/system_events.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1674069267506312e5108f90-08067928%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '763ea9f693b4d84c95cb65c478a8765c7acd6b67' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/admin/system_events.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1674069267506312e5108f90-08067928',
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
  'unifunc' => 'content_506312e51df1d0_08683627',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e51df1d0_08683627')) {function content_506312e51df1d0_08683627($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.changealert",false);?>



<script type="text/javascript">
	$(document).ready(function(){
		var openAtStart ="#system_events";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"systemEvents"), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"systemEvents",'fixed'=>true), 0);?>


<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	<h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System events<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
	<?php echo $_smarty_tpl->getSubTemplate ("./inc/toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('label_items'=>'events'), 0);?>

	</div>
</div>

<div class="mainfull">
	
	<?php echo $_smarty_tpl->getSubTemplate ("inc/form_events.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


</div>
<?php }} ?>