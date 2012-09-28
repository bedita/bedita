<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:43
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/documents/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2032417113504dfd98422647-96355311%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b26dd7f46a7a4b0678335b99d96aeb85c2e955ac' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/documents/inc/menuleft.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2032417113504dfd98422647-96355311',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd985019c9_50518806',
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'view' => 0,
    'tr' => 0,
    'currentModule' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd985019c9_50518806')) {function content_504dfd985019c9_50518806($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>
	
	<ul class="menuleft insidecol">
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='index'){?>class="on"<?php }?>><?php echo $_smarty_tpl->tpl_vars['tr']->value->link('Documents','/documents');?>
</li>
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='categories'){?>class="on"<?php }?>><?php echo $_smarty_tpl->tpl_vars['tr']->value->link('Categories','/documents/categories');?>
</li>
		<?php if ($_smarty_tpl->tpl_vars['view']->value->viewVars['module_modify']=='1'){?>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/view"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create new document<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
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