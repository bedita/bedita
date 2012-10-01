<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:54
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/modulesmenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:392463676504f16a27df8d1-19343364%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a517255e352c7c974be20b9045aa5c19b0d6e345' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/modulesmenu.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '392463676504f16a27df8d1-19343364',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504f16a29165f1_41517517',
  'variables' => 
  array (
    'moduleListInv' => 0,
    'mod' => 0,
    'html' => 0,
    'link' => 0,
    'moduleName' => 0,
    'stringSearched' => 0,
    'view' => 0,
    'sectionSel' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f16a29165f1_41517517')) {function content_504f16a29165f1_41517517($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
?><div class="modulesmenucaption">go to: &nbsp;<a>be</a></div>

<nav class="modulesmenu">
	
		<a title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="searchtrigger"></a>
		
		<!-- <a title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
help<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="helptrigger">?</a> -->
<?php if (!empty($_smarty_tpl->tpl_vars['moduleListInv']->value)){?><?php  $_smarty_tpl->tpl_vars['mod'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mod']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['moduleListInv']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mod']->key => $_smarty_tpl->tpl_vars['mod']->value){
$_smarty_tpl->tpl_vars['mod']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['mod']->key;
?><?php if (($_smarty_tpl->tpl_vars['mod']->value['status']=='on')){?><?php echo smarty_function_assign_concat(array('var'=>'link',1=>$_smarty_tpl->tpl_vars['html']->value->url('/'),2=>$_smarty_tpl->tpl_vars['mod']->value['url']),$_smarty_tpl);?>
<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['mod']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['mod']->value['name'])===null||$tmp==='' ? '' : $tmp);?>
 <?php if (($_smarty_tpl->tpl_vars['mod']->value['name']==(($tmp = @$_smarty_tpl->tpl_vars['moduleName']->value)===null||$tmp==='' ? '' : $tmp))){?> on<?php }?>"></a><?php }?><?php } ?><?php }?><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Bedita3 main dashboard<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="bedita"></a>
</nav>

	<form class="searchobjects" <?php if (!empty($_smarty_tpl->tpl_vars['stringSearched']->value)){?>style="display:block"<?php }?> 	action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['moduleName']->value)===null||$tmp==='' ? '' : $tmp);?>
/<?php echo $_smarty_tpl->tpl_vars['view']->value->action;?>
<?php if (!empty($_smarty_tpl->tpl_vars['sectionSel']->value)){?>/id:<?php echo $_smarty_tpl->tpl_vars['sectionSel']->value['id'];?>
<?php }?>" method="post">					
	<input type="text" placeholder="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" name="searchstring" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['stringSearched']->value)===null||$tmp==='' ? '' : $tmp);?>
"/>
	<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
GO<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
	</form>
	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu_dyn');?>
<?php }} ?>