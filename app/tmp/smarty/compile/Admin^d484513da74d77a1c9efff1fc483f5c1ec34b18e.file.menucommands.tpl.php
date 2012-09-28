<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:21
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/menucommands.tpl" */ ?>
<?php /*%%SmartyHeaderCode:47721224506312e57c3cb3-64313811%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd484513da74d77a1c9efff1fc483f5c1ec34b18e' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/menucommands.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '47721224506312e57c3cb3-64313811',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'fixed' => 0,
    'html' => 0,
    'currentModule' => 0,
    'moduleName' => 0,
    'back' => 0,
    'module_modify' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_506312e58f0918_92349763',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e58f0918_92349763')) {function content_506312e58f0918_92349763($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<div class="secondacolonna <?php if (!empty($_smarty_tpl->tpl_vars['fixed']->value)){?>fixed<?php }?>">
	
	<?php echo smarty_function_assign_concat(array('var'=>"back",1=>$_smarty_tpl->tpl_vars['html']->value->url('/'),2=>$_smarty_tpl->tpl_vars['currentModule']->value['url']),$_smarty_tpl);?>



	<div class="modules">
		<label class="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
" rel="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div> 
		
	<?php if ($_smarty_tpl->tpl_vars['module_modify']->value=='1'){?>
	<div class="insidecol">

	
		<?php if ($_smarty_tpl->tpl_vars['view']->value->action=="systemEvents"){?>
		
			<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/deleteEventLog');?>
" method="post">
			<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
			</form>

		<?php }elseif($_smarty_tpl->tpl_vars['view']->value->action=="systemLogs"){?>
		
			<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/emptySystemLog');?>
" method="post">
			<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
empty all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
			</form>

		<?php }elseif($_smarty_tpl->tpl_vars['view']->value->action=="emailInfo"){?>

			<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/deleteAllMailUnsent');?>
" method="post">
			<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
*"/>
				<p style="margin:10px; padding:10px; border-top:1px solid gray; border-bottom:1px solid gray; font-style:italic">
					* <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
does not affect email newsletter<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				</p>
			</form>
		
		<?php }elseif($_smarty_tpl->tpl_vars['view']->value->action=="emailLogs"){?>

			<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/deleteAllMailLogs');?>
" method="post">
			<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
			</form>

		<?php }elseif($_smarty_tpl->tpl_vars['view']->value->action=="viewConfig"){?>

			<input class="bemaincommands" type="button" name="save" onClick="$('#configForm').submit()"
			value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />

		<?php }elseif($_smarty_tpl->tpl_vars['view']->value->action=="sortModules"){?>

			<input class="bemaincommands" type="button" name="save" onClick="$('#sortModules').submit()" 
			value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />

		<?php }?>
	</div>
	<?php }?>



</div>
	


	



<?php }} ?>