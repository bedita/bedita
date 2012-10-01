<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:38:17
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/galleries/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:509613924504dff5fb4f269-87653173%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a335a72c5b3580a561518f3ad586c38c3d36d9e' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/galleries/inc/menuleft.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '509613924504dff5fb4f269-87653173',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dff5fc83477_65373122',
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'module_modify' => 0,
    'currentModule' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dff5fc83477_65373122')) {function content_504dff5fc83477_65373122($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>

	

	<ul class="menuleft insidecol">
	<?php if ($_smarty_tpl->tpl_vars['module_modify']->value=='1'){?>

		<li><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/view"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create new gallery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>

	<?php }?>
	</ul>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('export');?>


	<?php if ((!empty($_smarty_tpl->tpl_vars['view']->value->action))&&$_smarty_tpl->tpl_vars['view']->value->action=="index"){?>
	<div class="insidecol publishingtree">
		
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('tree');?>

	
	</div>
	<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>


</div><?php }} ?>