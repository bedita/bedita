<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:37
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:102513478050535c5034aa01-86679496%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b6b1e6cd5344c8058721d121a001e3468dc150b' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/menuleft.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '102513478050535c5034aa01-86679496',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50535c50462bd9_74258429',
  'variables' => 
  array (
    'view' => 0,
    'html' => 0,
    'conf' => 0,
    'module_modify' => 0,
    'method' => 0,
    'currentModule' => 0,
    'tree' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c50462bd9_74258429')) {function content_50535c50462bd9_74258429($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php $_smarty_tpl->tpl_vars['method'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['view']->value->action)===null||$tmp==='' ? 'index' : $tmp), null, 0);?>

<div class="primacolonna">
		
	<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>
		
	
	<?php if ($_smarty_tpl->tpl_vars['module_modify']->value=='1'){?>
	<ul class="menuleft insidecol">
		<li id="newArea" <?php if ($_smarty_tpl->tpl_vars['method']->value=='viewArea'){?>class="on"<?php }?>>
			<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/viewArea">
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
new publication<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</a>
		</li>
		<li id="newSection" <?php if ($_smarty_tpl->tpl_vars['method']->value=='viewSection'){?>class="on"<?php }?>>
			<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/viewSection">
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
new section<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</a>
		</li>
	</ul>
	<?php }?>
	
	<?php if (($_smarty_tpl->tpl_vars['method']->value!="viewArea"&&$_smarty_tpl->tpl_vars['method']->value!="viewSection")){?>
	<div class="insidecol publishingtree">	
			<?php if (!empty($_smarty_tpl->tpl_vars['tree']->value)){?>
			
			<?php echo $_smarty_tpl->tpl_vars['view']->value->element('tree');?>

			
			<?php }?>
	</div>
	<?php }?>
	
	<div style="margin-top:40px;">
	
	</div>
	

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>

	
</div>





<?php }} ?>