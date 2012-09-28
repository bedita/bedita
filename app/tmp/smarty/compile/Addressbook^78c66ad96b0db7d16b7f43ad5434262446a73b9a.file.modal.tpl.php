<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:06
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1216502127504ef5dad52f88-75755070%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '78c66ad96b0db7d16b7f43ad5434262446a73b9a' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/modal.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1216502127504ef5dad52f88-75755070',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5dad5f293_73339649',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5dad5f293_73339649')) {function content_504ef5dad5f293_73339649($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<div id="modaloverlay"></div>
<div id="modal">
	<div id="modalheader"><span class="caption"></span><a class="close"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
close<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></div>
	<div id="modalmain"></div>
</div><?php }} ?>