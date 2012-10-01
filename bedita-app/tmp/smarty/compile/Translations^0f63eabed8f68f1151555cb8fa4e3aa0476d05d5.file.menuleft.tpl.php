<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:54
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/translations/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1145000581504f16a2a637f6-32470888%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0f63eabed8f68f1151555cb8fa4e3aa0476d05d5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/translations/inc/menuleft.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1145000581504f16a2a637f6-32470888',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504f16a2af16e4_48021133',
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f16a2af16e4_48021133')) {function content_504f16a2af16e4_48021133($_smarty_tpl) {?>


<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>


	
	


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>



</div>
<?php }} ?>