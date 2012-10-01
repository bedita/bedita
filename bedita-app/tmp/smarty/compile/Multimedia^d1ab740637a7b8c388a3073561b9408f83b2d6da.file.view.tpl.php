<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:00
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1748229463504ef6d96a5e12-49883528%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd1ab740637a7b8c388a3073561b9408f83b2d6da' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/view.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1748229463504ef6d96a5e12-49883528',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6d9789d20_86239820',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6d9789d20_86239820')) {function content_504ef6d9789d20_86239820($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.datepicker",false);?>


<script type="text/javascript">
<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['uri'])){?>
    $(document).ready(function(){
		openAtStart("#multimediaitem");
    });
<?php }else{ ?>
    $(document).ready(function(){
		openAtStart("#title,#mediatypes");
    });
<?php }?>
</script>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_common_js');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="head">
	<h1><?php if (!empty($_smarty_tpl->tpl_vars['object']->value)){?><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? "<i>[no title]</i>" : $tmp);?>
<?php }else{ ?><i>[<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
]</i><?php }?></h1>
</div>

<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('fixed'=>true), 0);?>


<div class="main">

	<?php echo $_smarty_tpl->getSubTemplate ("inc/form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
	

</div>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('menuright');?>
<?php }} ?>