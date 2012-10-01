<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:37:14
         compiled from "/home/bato/workspace/bedita-plugins/glossary/views/elements/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1435395692504f145aba1158-00841063%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c6d152d924a02241b9a4959c66790d4ebe4d5cdb' => 
    array (
      0 => '/home/bato/workspace/bedita-plugins/glossary/views/elements/menuleft.tpl',
      1 => 1287751425,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1435395692504f145aba1158-00841063',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'method' => 0,
    'view' => 0,
    'html' => 0,
    'conf' => 0,
    'tr' => 0,
    'module_modify' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f145acccb86_22293411',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f145acccb86_22293411')) {function content_504f145acccb86_22293411($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->set("method",$_smarty_tpl->tpl_vars['method']->value);?>

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element("messages");?>

	
		<ul class="menuleft insidecol">
		<li <?php if ($_smarty_tpl->tpl_vars['method']->value=='index'){?>class="on"<?php }?>><?php echo $_smarty_tpl->tpl_vars['tr']->value->link('Glossary','/glossary');?>
</li>
		<li <?php if ($_smarty_tpl->tpl_vars['method']->value=='categories'){?>class="on"<?php }?>><?php echo $_smarty_tpl->tpl_vars['tr']->value->link('Categories','/glossary/categories');?>
</li>
		<?php if ($_smarty_tpl->tpl_vars['module_modify']->value=='1'){?>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/glossary/view');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create new glossary term<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
		<?php }?>
	</ul>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element("export");?>


<?php if ((!empty($_smarty_tpl->tpl_vars['method']->value))&&$_smarty_tpl->tpl_vars['method']->value=="index"){?>

		<div class="insidecol publishingtree">
			
			<?php echo $_smarty_tpl->tpl_vars['view']->value->element("tree");?>

		
		</div>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element("previews");?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element("user_module_perms");?>


</div><?php }} ?>