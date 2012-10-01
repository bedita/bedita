<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:54
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/translations/inc/menucommands.tpl" */ ?>
<?php /*%%SmartyHeaderCode:241928737504f16a2bdffe7-68825072%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '72abb1946962422aa87903d58bf88426fa41f9af' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/translations/inc/menucommands.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '241928737504f16a2bdffe7-68825072',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504f16a2d27a94_38254623',
  'variables' => 
  array (
    'object_master' => 0,
    'delparam' => 0,
    'html' => 0,
    'fixed' => 0,
    'moduleName' => 0,
    'currentModule' => 0,
    'session' => 0,
    'view' => 0,
    'module_modify' => 0,
    'object_translation' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f16a2d27a94_38254623')) {function content_504f16a2d27a94_38254623($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value)){?>
<?php echo smarty_function_assign_concat(array('var'=>'back_url',1=>"/",2=>$_smarty_tpl->tpl_vars['object_master']->value['ObjectType']['module_name'],3=>"/view/",4=>$_smarty_tpl->tpl_vars['object_master']->value['id']),$_smarty_tpl);?>

<script type="text/javascript">

$(document).ready(function(){
	$("#delLangText").submitConfirm({
		
		action: "<?php if (!empty($_smarty_tpl->tpl_vars['delparam']->value)){?><?php echo $_smarty_tpl->tpl_vars['html']->value->url($_smarty_tpl->tpl_vars['delparam']->value);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['html']->value->url('delete/');?>
<?php }?>",
		message: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete the item?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
",
		formId: "updateForm"
		
	});

	
	var urlBack = '<?php echo $_smarty_tpl->tpl_vars['html']->value->url(((string)$_smarty_tpl->tpl_vars['back_url']->value));?>
';
	$("#backBEObject").click(function() {
		document.location = urlBack;
	});
});
</script>

<?php }?>

<div class="secondacolonna <?php if (!empty($_smarty_tpl->tpl_vars['fixed']->value)){?>fixed<?php }?>">
	<div class="modules">
		<label class="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/translations');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div>
	
	<?php $_smarty_tpl->tpl_vars["user"] = new Smarty_variable($_smarty_tpl->tpl_vars['session']->value->read('BEAuthUser'), null, 0);?>

	<?php if (!empty($_smarty_tpl->tpl_vars['view']->value->action)&&$_smarty_tpl->tpl_vars['view']->value->action!="index"){?> 
	<div class="insidecol">
		<?php if ($_smarty_tpl->tpl_vars['module_modify']->value=='1'){?>		
			<input class="bemaincommands" type="button" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 " name="save" />
			<input class="bemaincommands" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" name="delete" id="delLangText" <?php if (!((($tmp = @$_smarty_tpl->tpl_vars['object_translation']->value['id'])===null||$tmp==='' ? false : $tmp))){?>disabled="1"<?php }?> />
		<?php }?>
		<input class="bemaincommands" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Back to <?php echo $_smarty_tpl->tpl_vars['object_master']->value['ObjectType']['name'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" name="back" id="backBEObject"/>
	</div>
	<?php }?>

</div>
<?php }} ?>