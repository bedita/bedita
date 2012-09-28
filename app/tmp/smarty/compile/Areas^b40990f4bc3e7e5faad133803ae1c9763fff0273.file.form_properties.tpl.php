<?php /* Smarty version Smarty-3.1.11, created on 2012-09-19 15:31:30
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/form_properties.tpl" */ ?>
<?php /*%%SmartyHeaderCode:97462240250535c68564647-61938265%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b40990f4bc3e7e5faad133803ae1c9763fff0273' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/form_properties.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97462240250535c68564647-61938265',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50535c68871a56_00580381',
  'variables' => 
  array (
    'html' => 0,
    'currLang' => 0,
    'view' => 0,
    'object' => 0,
    'parent_id' => 0,
    'tree' => 0,
    'beTree' => 0,
    'conf' => 0,
    'BEAuthUser' => 0,
    'val' => 0,
    'object_lang' => 0,
    'label' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c68871a56_00580381')) {function content_50535c68871a56_00580381($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_html_radios')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/function.html_radios.php';
?><fieldset id="properties">

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.datepicker",false);?>

<?php if ($_smarty_tpl->tpl_vars['currLang']->value!="eng"){?>
<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/i18n/ui.datepicker-".((string)$_smarty_tpl->tpl_vars['currLang']->value).".js",false);?>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_common_js');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('texteditor');?>


<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? null : $tmp);?>
"/>
	
	<table class="areaform">

			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" id="titleBEObject" style="width:100%" name="data[title]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? '' : $tmp);?>
"/></td>
			</tr>
			<tr>
				<td><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reside in<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label></td>
				<td>
					<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[parent_id]">
					<?php if (!empty($_smarty_tpl->tpl_vars['parent_id']->value)){?>
						<?php echo $_smarty_tpl->tpl_vars['beTree']->value->option($_smarty_tpl->tpl_vars['tree']->value,$_smarty_tpl->tpl_vars['parent_id']->value);?>

					<?php }else{ ?>
						<?php echo $_smarty_tpl->tpl_vars['beTree']->value->option($_smarty_tpl->tpl_vars['tree']->value);?>

					<?php }?>
					</select>
					
					<?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? false : $tmp)&&($_smarty_tpl->tpl_vars['object']->value['fixed']==1)){?>
						<input id="areaSectionAssoc" type="hidden" name="data[parent_id]" value="<?php echo $_smarty_tpl->tpl_vars['parent_id']->value;?>
" />
					<?php }?>
					
				</td>
			</tr>
			<tr>
					<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
					<td><textarea style="width:100%" class="mceSimple" name="data[description]"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['description'])===null||$tmp==='' ? '' : $tmp);?>
</textarea></td>
			</tr>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
unique name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<br />(<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
url name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
):</th>
				<td>
					<input id="nicknameBEObject" type="text" name="data[nickname]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['nickname'])===null||$tmp==='' ? null : $tmp);?>
" />
				</td>
			</tr>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td id="status">
				<?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['fixed'])===null||$tmp==='' ? '' : $tmp)==1){?>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This object is fixed - some data is readonly<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

					<input type="hidden" name="data[status]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['status'];?>
" />
				<?php }else{ ?>
					<?php echo smarty_function_html_radios(array('name'=>"data[status]",'options'=>$_smarty_tpl->tpl_vars['conf']->value->statusOptions,'selected'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value['status'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->defaultStatus : $tmp),'separator'=>" "),$_smarty_tpl);?>

				<?php }?>	
				<?php if (in_array('administrator',$_smarty_tpl->tpl_vars['BEAuthUser']->value['groups'])){?>
					&nbsp;&nbsp;&nbsp; <b>fixed</b>:&nbsp;&nbsp;<input type="checkbox" name="data[fixed]" value="1" <?php if (!empty($_smarty_tpl->tpl_vars['object']->value['fixed'])){?>checked<?php }?> />
				<?php }else{ ?>
					<input type="hidden" name="data[fixed]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['fixed'])===null||$tmp==='' ? 0 : $tmp);?>
" />
				<?php }?>				
				</td>
			</tr>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
creator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td>
					<input style="width:100%" type="text" name="data[creator]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['creator'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
" />
				</td>
				
			</tr>	
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
main language<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td>
				<?php $_smarty_tpl->tpl_vars['object_lang'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['object']->value['lang'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->defaultLang : $tmp), null, 0);?>
				<select name="data[lang]" id="main_lang">
					<?php  $_smarty_tpl->tpl_vars['label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['label']->_loop = false;
 $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->langOptions; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['label']->key => $_smarty_tpl->tpl_vars['label']->value){
$_smarty_tpl->tpl_vars['label']->_loop = true;
 $_smarty_tpl->tpl_vars['val']->value = $_smarty_tpl->tpl_vars['label']->key;
?>
					<option <?php if ($_smarty_tpl->tpl_vars['val']->value==$_smarty_tpl->tpl_vars['object_lang']->value){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</option>
					<?php } ?>
					<?php  $_smarty_tpl->tpl_vars['label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['label']->_loop = false;
 $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->langsIso; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['label']->key => $_smarty_tpl->tpl_vars['label']->value){
$_smarty_tpl->tpl_vars['label']->_loop = true;
 $_smarty_tpl->tpl_vars['val']->value = $_smarty_tpl->tpl_vars['label']->key;
?>
					<option <?php if ($_smarty_tpl->tpl_vars['val']->value==$_smarty_tpl->tpl_vars['object_lang']->value){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</option>
					<?php } ?>
				</select>
				</td>
			</tr>
			<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
visibility<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<input type="checkbox" name="data[menu]" value="1" <?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['menu'])===null||$tmp==='' ? 1 : $tmp)!='0'){?>checked<?php }?>/>
				 <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Visible in menu and canonical paths<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</td>
		</tr>
		<tr>
			<th>syndicate:</th>
				<td>
					<div class="ico_rss <?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['syndicate'])===null||$tmp==='' ? 'off' : $tmp)=='on'){?>on<?php }?>" 
					style="float:left; vertical-align:middle; margin-right:10px; width:24px; height:24px;">&nbsp;</div>
					<input style="margin-top:4px" type="checkbox" 
					onclick="$('.ico_rss').toggleClass('on')"
					name="data[syndicate]" value="on" <?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['syndicate'])===null||$tmp==='' ? 'off' : $tmp)=='on'){?>checked<?php }?> />
				</td>
			</tr>
			<tr>
			
					<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
					<td>
				<input type="radio" name="data[priority_order]" value="asc" <?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['priority_order'])===null||$tmp==='' ? 'asc' : $tmp)=="asc"){?>checked<?php }?> /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Insertion order, oldest contents first<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				<input type="radio" name="data[priority_order]" value="desc" <?php if ((($tmp = @$_smarty_tpl->tpl_vars['object']->value['priority_order'])===null||$tmp==='' ? 'asc' : $tmp)=="desc"){?>checked<?php }?> /><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Latest/newest contents first<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

					</td>
			</tr>
	</table>
	
</fieldset><?php }} ?>