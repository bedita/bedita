<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:37:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/previews.tpl" */ ?>
<?php /*%%SmartyHeaderCode:971092231504f145aea2149-98638912%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f3d04ccd6c4268b4a0b95eb3a6748389f303e4bb' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/previews.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '971092231504f145aea2149-98638912',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'previews' => 0,
    'preview' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f145af1e884_34021724',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f145af1e884_34021724')) {function content_504f145af1e884_34021724($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<?php if (!empty($_smarty_tpl->tpl_vars['previews']->value)){?>
	<ul class="menuleft insidecol">
		<li>
			<a href="javascript:void(0)" onclick="$('#previews').slideToggle();"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Previews<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			<ul id="previews" style="display:none;">
			<?php  $_smarty_tpl->tpl_vars["preview"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["preview"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['previews']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["preview"]->key => $_smarty_tpl->tpl_vars["preview"]->value){
$_smarty_tpl->tpl_vars["preview"]->_loop = true;
?>
				<li><a title="<?php echo $_smarty_tpl->tpl_vars['preview']->value['kurl'];?>
: <?php echo $_smarty_tpl->tpl_vars['preview']->value['url'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['preview']->value['url'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['preview']->value['desc'];?>
</a></li>
			<?php } ?>
			</ul>
		</li>
	</ul>
<?php }?>
<?php }} ?>