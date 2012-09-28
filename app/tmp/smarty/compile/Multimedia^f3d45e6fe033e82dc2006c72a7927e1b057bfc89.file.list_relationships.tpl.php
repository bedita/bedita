<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/list_relationships.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1134882078504ef6da904839-31728172%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f3d45e6fe033e82dc2006c72a7927e1b057bfc89' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/list_relationships.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1134882078504ef6da904839-31728172',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6da9a0a59_45678325',
  'variables' => 
  array (
    'object' => 0,
    'name' => 0,
    'related' => 0,
    'o' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6da9a0a59_45678325')) {function content_504ef6da9a0a59_45678325($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Referenced in<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="relationships">
	<?php if (empty($_smarty_tpl->tpl_vars['object']->value['relations'])){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No references<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php }else{ ?>
		<?php  $_smarty_tpl->tpl_vars["related"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["related"]->_loop = false;
 $_smarty_tpl->tpl_vars["name"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['object']->value['relations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["related"]->key => $_smarty_tpl->tpl_vars["related"]->value){
$_smarty_tpl->tpl_vars["related"]->_loop = true;
 $_smarty_tpl->tpl_vars["name"]->value = $_smarty_tpl->tpl_vars["related"]->key;
?>
		<h3><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
:</h3>
			<?php  $_smarty_tpl->tpl_vars["o"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["o"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['related']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["o"]->key => $_smarty_tpl->tpl_vars["o"]->value){
$_smarty_tpl->tpl_vars["o"]->_loop = true;
?>
			<ul class="bordered">
			
				<li><span title="<?php echo $_smarty_tpl->tpl_vars['o']->value['ObjectType']['name'];?>
" class="listrecent <?php echo $_smarty_tpl->tpl_vars['o']->value['ObjectType']['module_name'];?>
">&nbsp;</span>
				<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['o']->value['ObjectType']['module_name'];?>
/view/<?php echo $_smarty_tpl->tpl_vars['o']->value['id'];?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['o']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</a></li>
			
			</ul>
			<?php } ?>
		<?php } ?>
	<?php }?>


</fieldset>
<?php }} ?>