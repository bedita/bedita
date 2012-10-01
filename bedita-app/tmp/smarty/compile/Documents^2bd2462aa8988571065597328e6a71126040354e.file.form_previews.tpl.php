<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:48
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_previews.tpl" */ ?>
<?php /*%%SmartyHeaderCode:111491586504dfd9b1ff980-71298503%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2bd2462aa8988571065597328e6a71126040354e' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_previews.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '111491586504dfd9b1ff980-71298503',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9b339a89_71561339',
  'variables' => 
  array (
    'object' => 0,
    'previews' => 0,
    'pubs' => 0,
    'object_url' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9b339a89_71561339')) {function content_504dfd9b339a89_71561339($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
?><div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Previews<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="previewsTab">
<?php if (($_smarty_tpl->tpl_vars['object']->value['status']=="off")){?>
	<ul><li><i>Preview not available: status object is OFF</i></li></ul>
<?php }else{ ?>
<?php  $_smarty_tpl->tpl_vars["pubs"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["pubs"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['previews']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["pubs"]->key => $_smarty_tpl->tpl_vars["pubs"]->value){
$_smarty_tpl->tpl_vars["pubs"]->_loop = true;
?>

	<?php if (!empty($_smarty_tpl->tpl_vars['pubs']->value['object_url'][0]['public_url'])){?>
		<label><?php echo $_smarty_tpl->tpl_vars['pubs']->value['title'];?>
</label>
		<ul style="margin-bottom:10px">
		<?php  $_smarty_tpl->tpl_vars["object_url"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["object_url"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pubs']->value['object_url']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["object_url"]->key => $_smarty_tpl->tpl_vars["object_url"]->value){
$_smarty_tpl->tpl_vars["object_url"]->_loop = true;
?>
			<?php if (!empty($_smarty_tpl->tpl_vars['object_url']->value['public_url'])){?>
			<li style="border-bottom:1px solid gray; ">
			<a title="<?php echo $_smarty_tpl->tpl_vars['object_url']->value['public_url'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['object_url']->value['public_url'];?>
" target="_blank" rel="#nicknameBEObject">
				<?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['object_url']->value['public_url'],90,'(...)',true,true);?>
</a>
			</li>
			<?php }?>
		<?php } ?>
		</ul>
	<?php }else{ ?>
		<label><?php echo $_smarty_tpl->tpl_vars['pubs']->value['title'];?>
</label><ul style="margin-bottom:10px"><li style="border-bottom:1px solid gray;"><i>Preview not available: public URL is missing</i></li></ul>
	<?php }?>

	<?php if (!empty($_smarty_tpl->tpl_vars['pubs']->value['object_url'][0]['staging_url'])){?>	
		<label><?php echo $_smarty_tpl->tpl_vars['pubs']->value['title'];?>
 staging site</label>
		<ul style="margin-bottom:10px">
		<?php  $_smarty_tpl->tpl_vars["object_url"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["object_url"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pubs']->value['object_url']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["object_url"]->key => $_smarty_tpl->tpl_vars["object_url"]->value){
$_smarty_tpl->tpl_vars["object_url"]->_loop = true;
?>
			<?php if (!empty($_smarty_tpl->tpl_vars['object_url']->value['staging_url'])){?>
			<li style="border-bottom:1px solid gray; ">
			<a title="<?php echo $_smarty_tpl->tpl_vars['object_url']->value['staging_url'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['object_url']->value['staging_url'];?>
" target="_blank" rel="#nicknameBEObject">
				<?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['object_url']->value['staging_url'],90,'(…)',true,true);?>
</a>
			</li>
			<?php }?>
		<?php } ?>
		</ul>
	<?php }else{ ?>	
		<label><?php echo $_smarty_tpl->tpl_vars['pubs']->value['title'];?>
 staging site</label><ul style="margin-bottom:10px"><li style="border-bottom:1px solid gray;"><i>Preview not available: staging URL is missing</i></li></ul>
	<?php }?>

<?php }
if (!$_smarty_tpl->tpl_vars["pubs"]->_loop) {
?>
	
		<i><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No publication set<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</i>

<?php } ?>
<?php }?>
</fieldset>


<?php }} ?>