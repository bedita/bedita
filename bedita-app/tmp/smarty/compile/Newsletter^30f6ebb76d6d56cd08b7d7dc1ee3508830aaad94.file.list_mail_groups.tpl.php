<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:46
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_mail_groups.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14239648705053496eeee0c0-27044984%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '30f6ebb76d6d56cd08b7d7dc1ee3508830aaad94' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_mail_groups.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14239648705053496eeee0c0-27044984',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'mailGroups' => 0,
    'grp' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053496f085711_97792501',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053496f085711_97792501')) {function content_5053496f085711_97792501($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><script type="text/javascript">
var urlDelete = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('deleteMailGroups/');?>
";
var message = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete the item?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";

$(document).ready(function(){
	$(".delete").bind("click", function(){
		if(!confirm(message)) return false ;
		var groupId = $(this).attr("title");
		$("#form_"+groupId).attr("action", urlDelete).submit();
		return false;
	});

	$("input[type=text]").bind("keyup", function(){
		var text = $(this).val();
		if (jQuery.trim(text) == "") {
	   		$(this).parent().siblings().find("input[type=submit]").attr("disabled", "disabled");
		} else {
	   		$(this).parent().siblings().find("input[type=submit]").attr("disabled", "");
	    }
	});
	
});

</script>

	<table class="indexlist">

		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
list name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
subscribers<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
publication<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th>Id</th>
		</tr>

		<?php  $_smarty_tpl->tpl_vars["grp"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["grp"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['mailGroups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["grp"]->key => $_smarty_tpl->tpl_vars["grp"]->value){
$_smarty_tpl->tpl_vars["grp"]->_loop = true;
?>

			<tr rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/viewMailGroup/');?>
<?php echo $_smarty_tpl->tpl_vars['grp']->value['id'];?>
">
				<td>
					<?php echo $_smarty_tpl->tpl_vars['grp']->value['group_name'];?>

				</td>
				<td>
					<?php if ($_smarty_tpl->tpl_vars['grp']->value['visible']=="1"){?>
						public
					<?php }elseif($_smarty_tpl->tpl_vars['grp']->value['visible']=="0"){?>
						hidden
					<?php }?>
				</td>
				<td>
					<?php echo $_smarty_tpl->tpl_vars['grp']->value['subscribers'];?>

				</td>
				<td>
					<?php echo $_smarty_tpl->tpl_vars['grp']->value['publishing'];?>

				</td>
				<td><?php echo $_smarty_tpl->tpl_vars['grp']->value['id'];?>
</td>
			</tr>
			

		
		<?php }
if (!$_smarty_tpl->tpl_vars["grp"]->_loop) {
?>	
			<tr><td colspan="5"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No mail group found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td></tr>
		<?php } ?>		
		</table><?php }} ?>