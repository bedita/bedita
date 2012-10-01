<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_custom_properties.tpl" */ ?>
<?php /*%%SmartyHeaderCode:755890033504ef5e2ee9819-89502838%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3dda79627e29c065fbceaf200c0e6bd78c46ff1c' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_custom_properties.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '755890033504ef5e2ee9819-89502838',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'objectProperty' => 0,
    'prop' => 0,
    'countProperty' => 0,
    'opt' => 0,
    'choice' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e319d537_36210497',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e319d537_36210497')) {function content_504ef5e319d537_36210497($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?><?php if (!empty($_smarty_tpl->tpl_vars['objectProperty']->value)){?>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Custom Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="customProperties">
	
	<table class="indexlist" id="frmCustomProperties">
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	</tr>
	<?php $_smarty_tpl->tpl_vars["countProperty"] = new Smarty_variable("0", null, 0);?>
	<?php  $_smarty_tpl->tpl_vars["prop"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["prop"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['objectProperty']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["prop"]->key => $_smarty_tpl->tpl_vars["prop"]->value){
$_smarty_tpl->tpl_vars["prop"]->_loop = true;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['prop']->value['name'];?>
</td>
			<?php if ($_smarty_tpl->tpl_vars['prop']->value['property_type']=="options"){?>
	
				<?php if ($_smarty_tpl->tpl_vars['prop']->value['multiple_choice']==0){?>
					<td>
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_type]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['property_type'];?>
" />
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_id]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['id'];?>
" />
					<select name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value++;?>
][property_value]">
					<option value="">--</option>
					<?php  $_smarty_tpl->tpl_vars["opt"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["opt"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['prop']->value['PropertyOption']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["opt"]->key => $_smarty_tpl->tpl_vars["opt"]->value){
$_smarty_tpl->tpl_vars["opt"]->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['opt']->value['property_option'];?>
"<?php if ((($tmp = @$_smarty_tpl->tpl_vars['opt']->value['selected'])===null||$tmp==='' ? false : $tmp)){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['opt']->value['property_option'];?>
</option>
					<?php } ?>
					</select>		
					</td>
	
				<?php }else{ ?>
					
					<td>
					<?php  $_smarty_tpl->tpl_vars["choice"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["choice"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['prop']->value['PropertyOption']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["choice"]->key => $_smarty_tpl->tpl_vars["choice"]->value){
$_smarty_tpl->tpl_vars["choice"]->_loop = true;
?>
						<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_type]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['property_type'];?>
" />
						<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_id]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['id'];?>
" />
						<input type="checkbox" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value++;?>
][property_value]" value="<?php echo $_smarty_tpl->tpl_vars['choice']->value['property_option'];?>
"<?php if ((($tmp = @$_smarty_tpl->tpl_vars['choice']->value['selected'])===null||$tmp==='' ? false : $tmp)){?> checked="checked"<?php }?> />
						<?php echo $_smarty_tpl->tpl_vars['choice']->value['property_option'];?>
<br/>
					<?php } ?>
					</td>
					
				<?php }?>
				
			<?php }elseif($_smarty_tpl->tpl_vars['prop']->value['property_type']=="text"){?>
				<td>
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_type]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['property_type'];?>
" />
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_id]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['id'];?>
" />
					<textarea name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value++;?>
][property_value]" class="autogrowarea" style="overflow: hidden; width: 320px; line-height:1.2em;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['prop']->value['value']['property_value'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
				</td>
			<?php }elseif($_smarty_tpl->tpl_vars['prop']->value['property_type']=="number"){?>
				<td>
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_type]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['property_type'];?>
" />
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_id]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['id'];?>
" />
					<input type="text" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value++;?>
][property_value]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['prop']->value['value']['property_value'])===null||$tmp==='' ? '' : $tmp);?>
" />
				</td>	
			<?php }elseif($_smarty_tpl->tpl_vars['prop']->value['property_type']=="date"){?>
				<td>
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_type]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['property_type'];?>
" />
					<input type="hidden" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value;?>
][property_id]" value="<?php echo $_smarty_tpl->tpl_vars['prop']->value['id'];?>
" />
					<input size="10" type="text" style="vertical-align:middle"
						class="dateinput" name="data[ObjectProperty][<?php echo $_smarty_tpl->tpl_vars['countProperty']->value++;?>
][property_value]" 
						value="<?php echo smarty_modifier_date_format((($tmp = @$_smarty_tpl->tpl_vars['prop']->value['value']['property_value'])===null||$tmp==='' ? '' : $tmp),$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
" />			
				</td>
			<?php }?>
		</tr>
	
	<?php } ?> 
	</table>
	

</fieldset>
<?php }?><?php }} ?>