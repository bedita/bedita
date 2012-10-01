<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:37:15
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/json_meta_config.tpl" */ ?>
<?php /*%%SmartyHeaderCode:345907856504f145bebf4f3-83239566%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'db746ecbf41df1c512ee9cdf14860020887b68ad' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/json_meta_config.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '345907856504f145bebf4f3-83239566',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currLang' => 0,
    'currLang2' => 0,
    'session' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f145bf22763_10822118',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f145bf22763_10822118')) {function content_504f145bf22763_10822118($_smarty_tpl) {?>
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