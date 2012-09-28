<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/add_subscribers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1583489271505349717d71a9-03592893%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '220c82b18d5b469b1b36eb34cb8b82b7d2a1f7e5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/add_subscribers.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1583489271505349717d71a9-03592893',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_505349718037b9_85859889',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_505349718037b9_85859889')) {function content_505349718037b9_85859889($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add new subscribers<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="addsubscribers">
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Write here comma-separated email addresses<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<textarea name="addsubscribers" id="addsubscribers" style="width:100%" class="autogrowarea"></textarea>
</fieldset>
<?php }} ?>