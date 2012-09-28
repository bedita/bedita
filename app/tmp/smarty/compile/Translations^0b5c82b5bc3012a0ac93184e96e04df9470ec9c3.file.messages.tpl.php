<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:54
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/messages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1636489276504f16a32c3626-72156958%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0b5c82b5bc3012a0ac93184e96e04df9470ec9c3' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/messages.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1636489276504f16a32c3626-72156958',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504f16a33689c0_12602624',
  'variables' => 
  array (
    'session' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f16a33689c0_12602624')) {function content_504f16a33689c0_12602624($_smarty_tpl) {?><script type="text/javascript" charset="utf-8">
$(document).ready ( function ()  { 
<?php if (($_smarty_tpl->tpl_vars['session']->value->check('Message.info'))){?>
	$("#messagesDiv").triggerMessage("info", <?php echo $_smarty_tpl->tpl_vars['conf']->value->msgPause;?>
);
<?php }?>
<?php if (($_smarty_tpl->tpl_vars['session']->value->check('Message.warn'))){?>
	$("#messagesDiv").triggerMessage("warn", <?php echo $_smarty_tpl->tpl_vars['conf']->value->msgPause;?>
);
<?php }?>	
<?php if (($_smarty_tpl->tpl_vars['session']->value->check('Message.error'))){?>
	$("#messagesDiv").triggerMessage("error");
<?php }?>

});
</script>

<div id="messagesDiv">

	<?php if ($_smarty_tpl->tpl_vars['session']->value->check('Message.info')){?>
		<?php echo $_smarty_tpl->tpl_vars['session']->value->flash('info');?>

	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['session']->value->check('Message.warn')){?>
		<?php echo $_smarty_tpl->tpl_vars['session']->value->flash('warn');?>

	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['session']->value->check('Message.error')){?>
		<?php echo $_smarty_tpl->tpl_vars['session']->value->flash('error');?>

	<?php }?>
	
</div><?php }} ?>