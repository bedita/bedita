<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:38:17
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/modulesmenu_dyn.tpl" */ ?>
<?php /*%%SmartyHeaderCode:383529332504dff5fa18850-10150446%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7623645dd455df47a1c4b7fe9f7ec6ac93e0fdc0' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/modulesmenu_dyn.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '383529332504dff5fa18850-10150446',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dff5fb31807_13735169',
  'variables' => 
  array (
    'html' => 0,
    'moduleList' => 0,
    'mod' => 0,
    'link' => 0,
    'publications' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dff5fb31807_13735169')) {function content_504dff5fb31807_13735169($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
?><div class="modules modulesmenu_d shadow">	
	<div style="position:absolute; width:10px; top:0px; left:-10px; height:140px; background-color:white;"></div>
	<nav>
		<ul>
			<li class="index"><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
" title="BEdita home">home</a></li>
		<?php if (!empty($_smarty_tpl->tpl_vars['moduleList']->value)){?>
		<?php  $_smarty_tpl->tpl_vars['mod'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mod']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['moduleList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mod']->key => $_smarty_tpl->tpl_vars['mod']->value){
$_smarty_tpl->tpl_vars['mod']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['mod']->key;
?>
		<?php if (($_smarty_tpl->tpl_vars['mod']->value['status']=='on')){?>
			<?php echo smarty_function_assign_concat(array('var'=>'link',1=>$_smarty_tpl->tpl_vars['html']->value->url('/'),2=>$_smarty_tpl->tpl_vars['mod']->value['url']),$_smarty_tpl);?>

			<li class="<?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
">
				<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['mod']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['mod']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
		<?php }?>
		<?php } ?>
		<?php }?>
		<?php if (!empty($_smarty_tpl->tpl_vars['publications']->value)){?>
		<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['publications']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
			<?php if (!empty($_smarty_tpl->tpl_vars['item']->value['public_url'])){?>
			<li class="index"><a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['item']->value['public_url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['public_name'];?>
 | <?php echo $_smarty_tpl->tpl_vars['item']->value['public_url'];?>
">
				<img class="smallicon" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconUrl.png"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['public_url'],32,'[…]',true,true);?>
</a></li>
			<?php }?>
		<?php } ?>
		<?php }?>
			<li class="index"><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/logout');?>
" title="Exit"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
exit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		</ul> 
	</nav>

</div>
<?php }} ?>