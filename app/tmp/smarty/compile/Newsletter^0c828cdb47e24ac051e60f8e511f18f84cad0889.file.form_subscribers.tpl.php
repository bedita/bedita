<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_subscribers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:167875896505349715341e9-78260858%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0c828cdb47e24ac051e60f8e511f18f84cad0889' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_subscribers.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '167875896505349715341e9-78260858',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'item' => 0,
    'groups' => 0,
    'group' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50534971677982_96494245',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50534971677982_96494245')) {function content_50534971677982_96494245($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php if (!empty($_smarty_tpl->tpl_vars['item']->value)){?>

<div class="tab"><h2>Subscribers</h2></div>

<fieldset id="divSubscribers">
		
	<div id="loaderListSubscribers" class="loader"></div>
	<div id="subscribers">
	<?php echo $_smarty_tpl->getSubTemplate ("inc/list_subscribers.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div>

</fieldset>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Operations on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span class="selecteditems evidence"></span> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
selected subscribers<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="bulk">
		<select name="operation" style="width:75px">
			<option value="copy"> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
copy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 </option>
			<option value="move"> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
move<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 </option>
		</select>
		&nbsp;to:&nbsp;
		<select name="destination">
			<?php if (!empty($_smarty_tpl->tpl_vars['groups']->value)){?>
			<?php  $_smarty_tpl->tpl_vars["group"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["group"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["group"]->key => $_smarty_tpl->tpl_vars["group"]->value){
$_smarty_tpl->tpl_vars["group"]->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['group']->value['MailGroup']['id']!=$_smarty_tpl->tpl_vars['item']->value['id']){?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['group']->value['MailGroup']['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value['MailGroup']['group_name'];?>
</option>
				<?php }?>
			<?php } ?>
			<?php }?>
		</select>
		<input id="assocCard" type="button" value=" ok " />
	
	<hr />
	
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
change status to:<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;&nbsp;
		<select style="width:75px" id="newStatus" name="newStatus">
			<option value="valid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
valid<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			<option value="blocked"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
blocked<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
		</select>
		<input id="changestatusSelected" type="button" value=" ok " />
	
	<hr />

	<input id="deleteSelected" type="button" value="X <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Unsubscribe selected items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
</fieldset>

<?php echo $_smarty_tpl->getSubTemplate ("inc/add_subscribers.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php }?><?php }} ?>