<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:06
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/menucommands.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1223681015504ef5da1627e7-27366531%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5af7423ce1c09305789cd1fb8db8a8a971e46bff' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/menucommands.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1223681015504ef5da1627e7-27366531',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'fixed' => 0,
    'view' => 0,
    'session' => 0,
    'html' => 0,
    'currentModule' => 0,
    'moduleName' => 0,
    'back' => 0,
    'module_modify' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5da288fa1_43784565',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5da288fa1_43784565')) {function content_504ef5da288fa1_43784565($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<div class="secondacolonna <?php if (!empty($_smarty_tpl->tpl_vars['fixed']->value)){?>fixed<?php }?>">
	<?php if (!empty($_smarty_tpl->tpl_vars['view']->value->action)&&$_smarty_tpl->tpl_vars['view']->value->action!="index"){?>
		<?php $_smarty_tpl->tpl_vars["back"] = new Smarty_variable($_smarty_tpl->tpl_vars['session']->value->read("backFromView"), null, 0);?>
	<?php }else{ ?>
		<?php echo smarty_function_assign_concat(array('var'=>"back",1=>$_smarty_tpl->tpl_vars['html']->value->url('/'),2=>$_smarty_tpl->tpl_vars['currentModule']->value['url']),$_smarty_tpl);?>

	<?php }?>

	<div class="modules">
		<label class="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
" rel="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div> 
	
	<?php $_smarty_tpl->tpl_vars["user"] = new Smarty_variable($_smarty_tpl->tpl_vars['session']->value->read('BEAuthUser'), null, 0);?>
	
	<?php if ($_smarty_tpl->tpl_vars['view']->value->action=="view"&&$_smarty_tpl->tpl_vars['module_modify']->value=='1'){?>
	<script type="text/javascript">
	
	$(document).ready(function() {
		var cloneButton = $("div.insidecol input[name='clone']");
		cloneButton.unbind("click");
		cloneButton.click(function() {
			var company = $('input:radio[name*=company]:checked').val();
			if (company == 0) {
				var cloneTitle=prompt("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
,<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
surname<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
",
						$("input[name='data[person][name]']").val() + "," +
						$("input[name='data[person][surname]']").val() +"-copy");
				if (cloneTitle) {
					var nameArr =  cloneTitle.split(",");
					$("input[name='data[person][name]']").attr("value",nameArr[0]);
					$("input[name='data[person][surname]']").attr("value",nameArr[1]);
					$("#updateForm").attr("action","<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
addressbook/cloneObject");
					$("#updateForm").submit();
				}
			} else {
				var cloneTitle=prompt("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
", $("input[name='data[cmp][company_name]']").val() +"-copy");
				if (cloneTitle) {
					$("input[name='data[cmp][company_name]']").attr("value",cloneTitle);
					$("#updateForm").attr("action","<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
addressbook/cloneObject");
					$("#updateForm").submit();
				}
			}
		});
	});
	
	</script>
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 " name="save" />
		<input class="bemaincommands" type="button" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
clone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 " name="clone" />
		<input class="bemaincommands" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" name="delete" id="delBEObject" />
	</div>
	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('prevnext');?>

	
	<?php }elseif($_smarty_tpl->tpl_vars['view']->value->action=="index"){?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('select_categories');?>


	<?php }?>



</div>

<?php }} ?>