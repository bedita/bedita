<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:13
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form_properties.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1419842408504ef5e1e8de94-13210215%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'de8fa5965ab98c551643b552d020c2a22456e412' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form_properties.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1419842408504ef5e1e8de94-13210215',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'conf' => 0,
    'object' => 0,
    'html' => 0,
    'moduleList' => 0,
    'comments' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e213c066_32557261',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e213c066_32557261')) {function content_504ef5e213c066_32557261($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_html_radios')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/function.html_radios.php';
?><script type="text/javascript">
<!--

function addUserToCard(id,username) {
	$("#user_id").attr("value",id);
	$("#user_name").text(username);
}

$(document).ready(function() {
	$("#remove_user").click(removeUserFromCard);
});

function removeUserFromCard() {
	$("#user_id").attr("value", "");
	$("#user_name").text(" - ");
	$("#remove_user").attr("disabled", "disabled");
}

//-->
</script>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="properties">

<table class="bordered">

	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="4">
			<?php echo smarty_function_html_radios(array('name'=>"data[status]",'options'=>$_smarty_tpl->tpl_vars['conf']->value->statusOptions,'selected'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value['status'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->defaultStatus : $tmp),'separator'=>"&nbsp;"),$_smarty_tpl);?>

		</td>
	</tr>

	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Username<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<span id="user_name">
				<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['User'])){?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/viewUser/');?>
<?php echo $_smarty_tpl->tpl_vars['object']->value['User'][0]['id'];?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['User'][0]['userid'])===null||$tmp==='' ? '' : $tmp);?>
</a>
				<?php }else{ ?>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
no user data<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				<?php }?>
			</span>
			<input type="hidden" id="user_id" name="data[ObjectUser][card][0][user_id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['User'][0]['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>
			<input type="hidden" name="data[ObjectUser][card][0][object_id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>
			<input type="hidden" name="data[ObjectUser][card][0][switch]" value="card"/>
			&nbsp;&nbsp;&nbsp;
			<?php if (empty($_smarty_tpl->tpl_vars['object']->value['User'])){?>
			<input type="button" class="modalbutton" name="edit" value="  <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
promote as user<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
  "
				rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/showUsers');?>
"
				title="USERS : select an item to associate" />
			<?php }else{ ?>
			<input id="remove_user" type="button" value="  <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
remove from users<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
  "/>
			<?php }?>
		</td>
	</tr>
	

	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
comments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="radio" name="data[comments]" value="off"<?php if (empty($_smarty_tpl->tpl_vars['object']->value['comments'])||$_smarty_tpl->tpl_vars['object']->value['comments']=='off'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
			<input type="radio" name="data[comments]" value="on"<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['comments'])&&$_smarty_tpl->tpl_vars['object']->value['comments']=='on'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Yes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<input type="radio" name="data[comments]" value="moderated"<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['comments'])&&$_smarty_tpl->tpl_vars['object']->value['comments']=='moderated'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Moderated<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			&nbsp;&nbsp;
			<?php if (isset($_smarty_tpl->tpl_vars['moduleList']->value['comments'])&&$_smarty_tpl->tpl_vars['moduleList']->value['comments']['status']=="on"){?>
				<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['num_of_comment'])){?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
comments/index/comment_object_id:<?php echo $_smarty_tpl->tpl_vars['object']->value['id'];?>
"><img style="vertical-align:middle" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconComments.gif" alt="comments" /> (<?php echo $_smarty_tpl->tpl_vars['object']->value['num_of_comment'];?>
) <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
view<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				<?php }?>
			<?php }?>
		</td>
	</tr>
	
	<?php if (isset($_smarty_tpl->tpl_vars['comments']->value)){?>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Display details in frontend<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="radio" name="data[privacy_level]" value="0"<?php if (empty($_smarty_tpl->tpl_vars['object']->value['privacy_level'])||$_smarty_tpl->tpl_vars['object']->value['privacy_level']=='0'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
			<input type="radio" name="data[privacy_level]" value="1"<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['privacy_level'])&&$_smarty_tpl->tpl_vars['object']->value['privacy_level']=='1'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Yes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		</td>
	</tr>
	<?php }?>

</table>

</fieldset>
<?php }} ?>