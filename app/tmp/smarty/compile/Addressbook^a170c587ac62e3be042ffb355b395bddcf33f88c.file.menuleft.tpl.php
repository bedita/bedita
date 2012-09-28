<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:05
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:596615780504ef5d9e74259-50024757%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a170c587ac62e3be042ffb355b395bddcf33f88c' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/menuleft.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '596615780504ef5d9e74259-50024757',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'module_modify' => 0,
    'currentModule' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5d9f1e271_35960234',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5d9f1e271_35960234')) {function content_504ef5d9f1e271_35960234($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<div class="primacolonna">

		<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>

		

		<ul class="menuleft insidecol">

		<?php if ($_smarty_tpl->tpl_vars['module_modify']->value=='1'){?>

			<li><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/view"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New card<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/categories"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>

		<?php }?>
			
		</ul>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('export');?>


<?php if ((!empty($_smarty_tpl->tpl_vars['view']->value->action))&&$_smarty_tpl->tpl_vars['view']->value->action=="index"){?>

		<div class="insidecol publishingtree">
			
			<?php echo $_smarty_tpl->tpl_vars['view']->value->element('tree');?>

		
		</div>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>

		
</div><?php }} ?>