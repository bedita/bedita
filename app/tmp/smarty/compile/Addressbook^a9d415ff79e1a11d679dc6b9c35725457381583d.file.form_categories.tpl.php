<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_categories.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1131587671504ef5e22b1d27-22000768%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a9d415ff79e1a11d679dc6b9c35725457381583d' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_categories.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1131587671504ef5e22b1d27-22000768',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'areaCategory' => 0,
    'areaName' => 0,
    'areaC' => 0,
    'cat' => 0,
    'object' => 0,
    'noareaC' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e23be8a7_19123327',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e23be8a7_19123327')) {function content_504ef5e23be8a7_19123327($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php if (!empty($_smarty_tpl->tpl_vars['areaCategory']->value)){?>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="category" >
	
<table class="bordered">
		
	<?php if (!empty($_smarty_tpl->tpl_vars['areaCategory']->value['area'])){?>
		<?php  $_smarty_tpl->tpl_vars["areaC"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["areaC"]->_loop = false;
 $_smarty_tpl->tpl_vars["areaName"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['areaCategory']->value['area']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["areaC"]->key => $_smarty_tpl->tpl_vars["areaC"]->value){
$_smarty_tpl->tpl_vars["areaC"]->_loop = true;
 $_smarty_tpl->tpl_vars["areaName"]->value = $_smarty_tpl->tpl_vars["areaC"]->key;
?>
			<tr>
				<td><h2 style="color:white; margin-left:-10px"><?php echo $_smarty_tpl->tpl_vars['areaName']->value;?>
</h2></td>
			</tr>
			<?php  $_smarty_tpl->tpl_vars["cat"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["cat"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['areaC']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["cat"]->key => $_smarty_tpl->tpl_vars["cat"]->value){
$_smarty_tpl->tpl_vars["cat"]->_loop = true;
?>
			<tr>
				<td>
				<input type="checkbox" id="cat_<?php echo $_smarty_tpl->tpl_vars['cat']->value['id'];?>
" 
					name="data[Category][<?php echo $_smarty_tpl->tpl_vars['cat']->value['id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['cat']->value['id'];?>
"
					<?php if ($_smarty_tpl->tpl_vars['object']->value&&in_array($_smarty_tpl->tpl_vars['cat']->value['id'],$_smarty_tpl->tpl_vars['object']->value['assocCategory'])){?>checked="checked"<?php }?>/>
				<label for="cat_<?php echo $_smarty_tpl->tpl_vars['cat']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['cat']->value['label'];?>
</label>
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
	
	<?php }?>
	
	<?php if (!empty($_smarty_tpl->tpl_vars['areaCategory']->value['noarea'])){?>
			
			<tr>
				<th><h2 style="color:white; margin-left:-10px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Generic categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></th>
			</tr>
			<?php  $_smarty_tpl->tpl_vars["noareaC"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["noareaC"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['areaCategory']->value['noarea']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["noareaC"]->key => $_smarty_tpl->tpl_vars["noareaC"]->value){
$_smarty_tpl->tpl_vars["noareaC"]->_loop = true;
?>
			<tr>
				<td>
				<input type="checkbox" id="cat_<?php echo $_smarty_tpl->tpl_vars['noareaC']->value['id'];?>
" 
					name="data[Category][<?php echo $_smarty_tpl->tpl_vars['noareaC']->value['id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['noareaC']->value['id'];?>
"
					<?php if ($_smarty_tpl->tpl_vars['object']->value&&in_array($_smarty_tpl->tpl_vars['noareaC']->value['id'],$_smarty_tpl->tpl_vars['object']->value['assocCategory'])){?>checked="checked"<?php }?>/>
				<label for="cat_<?php echo $_smarty_tpl->tpl_vars['noareaC']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['noareaC']->value['label'];?>
</label>
				</td>
			</tr>
			<?php } ?>
		<?php }?>

</table>

</fieldset>

<?php }?><?php }} ?>