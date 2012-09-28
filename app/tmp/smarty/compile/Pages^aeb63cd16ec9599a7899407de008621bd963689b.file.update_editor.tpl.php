<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:04
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/pages/update_editor.tpl" */ ?>
<?php /*%%SmartyHeaderCode:822648831504dfda446a998-53470952%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aeb63cd16ec9599a7899407de008621bd963689b' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/pages/update_editor.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '822648831504dfda446a998-53470952',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfda45ae9e2_72807504',
  'variables' => 
  array (
    'editors' => 0,
    'html' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfda45ae9e2_72807504')) {function content_504dfda45ae9e2_72807504($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
	
<?php if (count($_smarty_tpl->tpl_vars['editors']->value)>1){?>

	<script type="text/javascript">
		if ((autoSaveTimer !== false) && (autoSaveTimer != undefined))
			switchAutosave("off", false);
		else
			switchAutosave("off");
		
		$(".secondacolonna .modules label:not(.concurrentuser)")
		.addClass("concurrentuser")
		.attr("title","Warning! More users are editing this document")
		.tooltip({
			extraClass: "tip",
			fixPNG: true,
			top: 10,
			left: -90

		});
	</script>

	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Warning<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.<br/>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Concurrent editors:<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	<!-- <img src="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
img/iconConcurrentuser.png" style="float:left; vertical-align:middle; width:20px; margin-right:10px;" /> -->

	<ul id="editorsList" style="margin-bottom:10px">
	<?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['editors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value){
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
		<li rel="<?php echo $_smarty_tpl->tpl_vars['item']->value['User']['id'];?>
" style="border-bottom:1px solid gray">
			<b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['User']['realname'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['item']->value['User']['userid'] : $tmp);?>
</b>
		</li>
	<?php } ?>
	</ul>
	
<?php }else{ ?>

	<script type="text/javascript">
	if (autoSaveTimer === false) {
		var newStatus = $("input[name=data\\[status\\]]:checked").attr('value');
		if ((status != 'on') && (status == newStatus))
			switchAutosave("on");
	}
	$(".secondacolonna .modules label").removeClass("concurrentuser").tooltip({
			delay: 0
	});
	</script>
<?php }?><?php }} ?>