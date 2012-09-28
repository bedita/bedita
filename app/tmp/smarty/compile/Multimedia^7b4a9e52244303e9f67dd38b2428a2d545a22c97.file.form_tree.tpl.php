<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_tree.tpl" */ ?>
<?php /*%%SmartyHeaderCode:237883616504ef6da3210f1-67972257%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b4a9e52244303e9f67dd38b2428a2d545a22c97' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_tree.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '237883616504ef6da3210f1-67972257',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6da397627_44519265',
  'variables' => 
  array (
    'object' => 0,
    'tpl_title' => 0,
    'tree' => 0,
    'params' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6da397627_44519265')) {function content_504ef6da397627_44519265($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><script type="text/javascript">
<!--
<?php if ($_smarty_tpl->tpl_vars['object']->value['fixed']){?>
$(document).ready(function(){
		$("#whereto input[type=checkbox]").attr("disabled","disabled");
});
<?php }?>
//-->
</script>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php if (empty($_smarty_tpl->tpl_vars['tpl_title']->value)){?>Position<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['tpl_title']->value;?>
<?php }?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="whereto">
	<?php if ($_smarty_tpl->tpl_vars['object']->value['fixed']){?><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
The content is fixed: it's not possible to change the position in the tree<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?>
	
	<?php if (empty($_smarty_tpl->tpl_vars['tree']->value)){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No tree found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php }else{ ?>

		<div class="publishingtree" style="width:auto; margin-left:10px;">
		
			<?php echo smarty_function_assign_associative(array('var'=>"params",'checkbox'=>true),$_smarty_tpl);?>

			<?php echo $_smarty_tpl->tpl_vars['view']->value->element('tree',$_smarty_tpl->tpl_vars['params']->value);?>

		
		</div>
		
	<?php }?>
	
</fieldset>

<?php }} ?>