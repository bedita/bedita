<?php /* Smarty version Smarty-3.1.11, created on 2012-09-17 12:34:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/layouts/error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14100326185056fca6a45990-90794901%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fea9225a945ff4accfc1f1cb6402f7eb64f6026c' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/layouts/error.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14100326185056fca6a45990-90794901',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'beurl' => 0,
    'scripts_for_layout' => 0,
    'bodyClass' => 0,
    'view' => 0,
    'conf' => 0,
    'content_for_layout' => 0,
    'noFooter' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5056fca6b70b97_57057613',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5056fca6b70b97_57057613')) {function content_5056fca6b70b97_57057613($_smarty_tpl) {?><?php if (!is_callable('smarty_function_agent')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.agent.php';
?><?php echo smarty_function_agent(array('var'=>"agent"),$_smarty_tpl);?>

<!DOCTYPE html>
<html lang="it">
<head>
	<title>BEdita</title>


	<?php echo $_smarty_tpl->tpl_vars['html']->value->css('bedita.css?v=01');?>

	
	<!--[if lte IE 6]>
		<?php echo $_smarty_tpl->tpl_vars['html']->value->css('IE6fix');?>

	<![endif]-->

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.cookie");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.autogrow");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.dimensions");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("beditaUI");?>


	<?php echo $_smarty_tpl->tpl_vars['beurl']->value->addModuleScripts();?>


	
	<?php echo $_smarty_tpl->tpl_vars['scripts_for_layout']->value;?>


	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery-ui-1.8rc3.custom");?>

	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.draggable");?>


	
</head>
<body<?php if (!empty($_smarty_tpl->tpl_vars['bodyClass']->value)){?> class="<?php echo $_smarty_tpl->tpl_vars['bodyClass']->value;?>
"<?php }?>>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('messages');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<div class="primacolonna">
	<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>	
</div>

<div id="messagesDiv" style="margin-top:140px">
	<div class="message error">
		<?php echo $_smarty_tpl->tpl_vars['content_for_layout']->value;?>
	
	</div>
</div>

	


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('help');?>





<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modal');?>





<?php if (empty($_smarty_tpl->tpl_vars['noFooter']->value)){?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('footer');?>


<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('sql_dump');?>



</body>
</html>

<?php }} ?>