<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:15
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/home/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1979886736504dfcc824a583-00508532%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '053c389cdfad6d24421f9bf54ef3e6046407c2d3' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/home/inc/menuleft.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1979886736504dfcc824a583-00508532',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfcc82b3878_04676511',
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfcc82b3878_04676511')) {function content_504dfcc82b3878_04676511($_smarty_tpl) {?>
<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>

	

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>


</div><?php }} ?>