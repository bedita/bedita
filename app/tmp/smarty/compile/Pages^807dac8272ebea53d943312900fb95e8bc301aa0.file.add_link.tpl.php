<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:47:48
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/pages/add_link.tpl" */ ?>
<?php /*%%SmartyHeaderCode:960227322504f16d4c50ea8-37909435%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '807dac8272ebea53d943312900fb95e8bc301aa0' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/pages/add_link.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '960227322504f16d4c50ea8-37909435',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'session' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f16d4d4d188_83548417',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f16d4d4d188_83548417')) {function content_504f16d4d4d188_83548417($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_capitalize')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.capitalize.php';
?><?php if (($_smarty_tpl->tpl_vars['session']->value->check('Message.error'))){?>
	<div class="message-error">
		<div id="error-img"><span>&nbsp;</span></div>
		<p><?php echo smarty_modifier_capitalize($_smarty_tpl->tpl_vars['session']->value->flash('error'));?>
</p>
	</div>
<?php }else{ ?>
<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_link_item');?>

<?php }?><?php }} ?>