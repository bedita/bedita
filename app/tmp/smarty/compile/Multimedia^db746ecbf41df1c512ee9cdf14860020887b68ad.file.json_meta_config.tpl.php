<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:57
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/json_meta_config.tpl" */ ?>
<?php /*%%SmartyHeaderCode:172319647504e103604a7b1-45599942%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'db746ecbf41df1c512ee9cdf14860020887b68ad' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/json_meta_config.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '172319647504e103604a7b1-45599942',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e10360a2e66_86301330',
  'variables' => 
  array (
    'currLang' => 0,
    'currLang2' => 0,
    'session' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e10360a2e66_86301330')) {function content_504e10360a2e66_86301330($_smarty_tpl) {?>
<meta name="BEDITA.currLang" content="<?php echo $_smarty_tpl->tpl_vars['currLang']->value;?>
" />
<meta name="BEDITA.currLang2" content="<?php echo $_smarty_tpl->tpl_vars['currLang2']->value;?>
" />
<meta name="BEDITA.webroot" content="<?php echo $_smarty_tpl->tpl_vars['session']->value->webroot;?>
" />

<script type="text/javascript">

	// global json BEDITA config
	var BEDITA = {
		'currLang': '<?php echo $_smarty_tpl->tpl_vars['currLang']->value;?>
',
		'currLang2': '<?php echo $_smarty_tpl->tpl_vars['currLang2']->value;?>
',
		'webroot': '<?php echo $_smarty_tpl->tpl_vars['session']->value->webroot;?>
',
	};
	
</script>
<?php }} ?>