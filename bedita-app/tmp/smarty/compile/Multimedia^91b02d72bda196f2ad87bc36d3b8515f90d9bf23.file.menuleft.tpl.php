<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:55
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/menuleft.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1745570297504e10332d25d0-69013588%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '91b02d72bda196f2ad87bc36d3b8515f90d9bf23' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/menuleft.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1745570297504e10332d25d0-69013588',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e103336aa12_95109822',
  'variables' => 
  array (
    'method' => 0,
    'html' => 0,
    'conf' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e103336aa12_95109822')) {function content_504e103336aa12_95109822($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php $_smarty_tpl->tpl_vars['method'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['method']->value)===null||$tmp==='' ? 'index' : $tmp), null, 0);?>



	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>
		
		

		<ul class="menuleft insidecol">
			
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/multimedia/view');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add new item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>	
					
		</ul>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('export');?>


	<?php if ((!empty($_smarty_tpl->tpl_vars['view']->value->action))&&$_smarty_tpl->tpl_vars['view']->value->action=="index"){?>
	<div class="insidecol publishingtree">

		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('tree');?>


	</div>
	<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('user_module_perms');?>


</div>




<?php }} ?>