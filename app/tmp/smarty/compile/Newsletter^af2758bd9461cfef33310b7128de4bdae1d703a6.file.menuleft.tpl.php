<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:40
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19982029345053496802b114-94986710%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'af2758bd9461cfef33310b7128de4bdae1d703a6' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/menuleft.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19982029345053496802b114-94986710',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'method' => 0,
    'view' => 0,
    'html' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50534968140421_09376928',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50534968140421_09376928')) {function content_50534968140421_09376928($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo $_smarty_tpl->tpl_vars['view']->value->set("method",$_smarty_tpl->tpl_vars['method']->value);?>


<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>

	
		
	
		<ul class="menuleft insidecol">

			<li <?php if ($_smarty_tpl->tpl_vars['method']->value=="newsletters"){?>class="on"<?php }?>>
				<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/newsletters');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Newsletters<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
			
		</ul>
		
		<ul class="menuleft insidecol">
			<li <?php if ($_smarty_tpl->tpl_vars['method']->value=="mailgroups"){?>class="on"<?php }?>>
				<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/mailGroups');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subscriber lists<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
		</ul>
		
		<ul class="menuleft insidecol">

			<li <?php if ($_smarty_tpl->tpl_vars['method']->value=="invoices"){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/invoices');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoices<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
	
		</ul>

		<ul class="menuleft insidecol">	
			<li <?php if ($_smarty_tpl->tpl_vars['method']->value=="templates"){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/templates');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Templates<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		
		</ul>
		
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>

		

</div><?php }} ?>