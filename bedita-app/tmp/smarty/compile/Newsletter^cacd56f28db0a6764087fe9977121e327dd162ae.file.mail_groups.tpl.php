<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:46
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/mail_groups.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4728332565053496edb5b82-96160828%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cacd56f28db0a6764087fe9977121e327dd162ae' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/mail_groups.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4728332565053496edb5b82-96160828',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053496ee84221_07703982',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053496ee84221_07703982')) {function content_5053496ee84221_07703982($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.changealert",false);?>


	
<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"mailgroups"), 0);?>


<div class="head">
	
	<h1><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Lists<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h1>

</div>

<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"mailgroups"), 0);?>


<div class="mainfull">
	
<?php echo $_smarty_tpl->getSubTemplate ("inc/list_mail_groups.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"mailgroups"), 0);?>


</div><?php }} ?>