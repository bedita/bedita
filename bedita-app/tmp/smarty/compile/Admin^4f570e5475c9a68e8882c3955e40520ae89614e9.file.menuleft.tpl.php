<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:21
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1249566140506312e54425f0-32624172%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f570e5475c9a68e8882c3955e40520ae89614e9' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/menuleft.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1249566140506312e54425f0-32624172',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_506312e56d6711_24112296',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e56d6711_24112296')) {function content_506312e56d6711_24112296($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_block_bedev')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.bedev.php';
?>

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>


	<ul class="menuleft insidecol">
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='systemEvents'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/systemEvents');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System Events<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='systemInfo'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/systemInfo');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System Info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='systemLogs'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/systemLogs');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System Logs<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
	</ul>

	<ul class="menuleft insidecol">
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='viewConfig'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/viewConfig');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Configuration<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
	</ul>
	
	<ul class="menuleft insidecol">
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='emailInfo'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/emailInfo');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Mail Queue<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='emailLogs'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/emailLogs');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Mail Logs<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
	</ul>

	<ul class="menuleft insidecol">
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='customproperties'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/customproperties');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Custom properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('bedev', array()); $_block_repeat=true; echo smarty_block_bedev(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='customrelations'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/relations');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Custom relations<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_bedev(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	</ul>

	<ul class="menuleft insidecol">
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='coreModules'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/coreModules');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Core Modules<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='pluginModules'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/pluginModules');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Plugin Modules<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='sortModules'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/sortModules');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Sort Modules<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='addons'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/addons');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Addons<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
	</ul>

	<ul class="menuleft insidecol">
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('bedev', array()); $_block_repeat=true; echo smarty_block_bedev(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='importData'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/importData');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Import Data<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_bedev(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<li <?php if ($_smarty_tpl->tpl_vars['view']->value->action=='utility'){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/admin/utility');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Utility<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
	</ul>
	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>


</div><?php }} ?>