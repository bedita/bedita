<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:44
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/layouts/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1792341639504dfd9cbf8db1-43185856%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eafb8e7d81530c800a6f972ee85eedfd63a90a7b' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/layouts/default.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1792341639504dfd9cbf8db1-43185856',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9cdbd519_51665713',
  'variables' => 
  array (
    'currLang2' => 0,
    'currentModule' => 0,
    'html' => 0,
    'object' => 0,
    'agent' => 0,
    'view' => 0,
    'beurl' => 0,
    'scripts_for_layout' => 0,
    'bodyClass' => 0,
    'content_for_layout' => 0,
    'noFooter' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9cdbd519_51665713')) {function content_504dfd9cdbd519_51665713($_smarty_tpl) {?><?php if (!is_callable('smarty_function_agent')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.agent.php';
?><?php echo smarty_function_agent(array('var'=>"agent"),$_smarty_tpl);?>

<!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->tpl_vars['currLang2']->value;?>
">
<head>
	<title>BEdita | <?php echo (($tmp = @$_smarty_tpl->tpl_vars['currentModule']->value['label'])===null||$tmp==='' ? 'home' : $tmp);?>
 | <?php echo $_smarty_tpl->tpl_vars['html']->value->action;?>
 | <?php if (!empty($_smarty_tpl->tpl_vars['object']->value)){?><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? "<i>[no title]</i>" : $tmp);?>
<?php }?></title>

	<meta name="robots" content="noindex,nofollow"/>
	
	<?php if ($_smarty_tpl->tpl_vars['agent']->value['iPHONE']||$_smarty_tpl->tpl_vars['agent']->value['iPAD']){?>
		
		<meta name="viewport" content="user-scalable=yes, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
	    <meta name="apple-mobile-web-app-capable" content="yes" />
	    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
	    <link rel="apple-touch-icon" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/img/');?>
BElogo_iphone.png"/>
	    <link rel="apple-touch-startup-image" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/img/');?>
BElogo_iphone.png" />
		<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/css/');?>
beditaMobile.css" type="text/css" media="screen" title="main" charset="utf-8">
		
	<?php }?>
		
	<?php echo $_smarty_tpl->getSubTemplate ("inc/meta.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('json_meta_config');?>


	<?php echo $_smarty_tpl->tpl_vars['html']->value->css('bedita.css?v=01');?>

	
	<!--[if lte IE 6]>
		<?php echo $_smarty_tpl->tpl_vars['html']->value->css('IE6fix');?>

	<![endif]-->

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.cookie");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.autogrow");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.dimensions");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.tooltip.min");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("beditaUI");?>


	<?php echo $_smarty_tpl->tpl_vars['beurl']->value->addModuleScripts();?>


	
	<?php echo $_smarty_tpl->tpl_vars['scripts_for_layout']->value;?>


	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery-ui-1.8rc3.custom");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.draggable");?>


	
</head>
<body<?php if (!empty($_smarty_tpl->tpl_vars['bodyClass']->value)){?> class="<?php echo $_smarty_tpl->tpl_vars['bodyClass']->value;?>
"<?php }?>>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('messages');?>


<?php echo $_smarty_tpl->tpl_vars['content_for_layout']->value;?>

	


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('help');?>





<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modal');?>





<?php if (empty($_smarty_tpl->tpl_vars['noFooter']->value)){?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('footer');?>


<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('sql_dump');?>



</body>
</html><?php }} ?>