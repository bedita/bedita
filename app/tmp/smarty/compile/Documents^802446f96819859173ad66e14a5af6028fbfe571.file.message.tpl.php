<?php /* Smarty version Smarty-3.1.11, created on 2012-09-10 16:52:10
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/message.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2085727729504dfe9af0d470-75895357%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '802446f96819859173ad66e14a5af6028fbfe571' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/message.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2085727729504dfe9af0d470-75895357',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'class' => 0,
    'message' => 0,
    'detail' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504dfe9b0f0634_19232158',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfe9b0f0634_19232158')) {function content_504dfe9b0f0634_19232158($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><div class="message <?php echo $_smarty_tpl->tpl_vars['class']->value;?>
"><?php if ($_smarty_tpl->tpl_vars['class']->value=="info"){?><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Notice<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2><?php }elseif($_smarty_tpl->tpl_vars['class']->value=="warn"){?><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Warning<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2><?php }elseif($_smarty_tpl->tpl_vars['class']->value=="error"){?><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Error<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2><?php }?><p style="display:block; margin-top:10px"><?php echo $_smarty_tpl->tpl_vars['message']->value;?>
</p><hr /><?php if (!empty($_smarty_tpl->tpl_vars['detail']->value)){?><a class="close" href="javascript:void(0)" onclick="$('.messageDetail').toggle()"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
see error detail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a><?php }?><a class="close" href="javascript:void(0)" onClick="$('#messagesDiv').fadeOut()"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
close<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></div><?php if (!empty($_smarty_tpl->tpl_vars['detail']->value)){?><div class="messageDetail shadow" style="display:none"><p style="font-family:monospace;"><?php echo $_smarty_tpl->tpl_vars['detail']->value;?>
</p><hr /><a class="close" href="javascript:void(0)" onClick="$('.messageDetail').fadeOut()"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
close<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></div><?php }?><?php }} ?>