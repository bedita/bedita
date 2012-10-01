<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:38
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/form_area.tpl" */ ?>
<?php /*%%SmartyHeaderCode:27930331050535c513e2302-78686330%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '53bf5c78aee6d59a2d23e6a656d362f0b29e14eb' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/form_area.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27930331050535c513e2302-78686330',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50535c518c9377_27440130',
  'variables' => 
  array (
    'view' => 0,
    'object' => 0,
    'conf' => 0,
    'BEAuthUser' => 0,
    'val' => 0,
    'object_lang' => 0,
    'label' => 0,
    'code' => 0,
    'lic' => 0,
    'params' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c518c9377_27440130')) {function content_50535c518c9377_27440130($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_html_radios')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/function.html_radios.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><fieldset id="properties">	

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_common_js');?>


<?php $_smarty_tpl->tpl_vars['object_lang'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['object']->value['lang'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->defaultLang : $tmp), null, 0);?>
	
	<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>
	<input type="hidden" name="data[title]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? '' : $tmp);?>
"/>



	<table class="areaform" border=0 style="margin-bottom:10px">

		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<input id="titleBEObject" type="text" name="data[title]"	value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
" />
			</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
public name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<input type="text" name="data[public_name]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['public_name'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
""/>
			</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<textarea class="mceSimple" name="data[description]"><?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['description'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
</textarea>
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
	


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
More properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<div id="moreproperties">
	<table class="areaform">
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
main language<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
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
nickname<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input id="nicknameBEObject" type="text" name="data[nickname]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['nickname'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
" />
		</td>
	</tr>
			</tr>
				<tr>
				<th>id:</th>
				<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? null : $tmp);?>
</td>
			</tr>
	</table>
	
	<hr />
	
	<table class="areaform">
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
public url<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[public_url]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['public_url'])===null||$tmp==='' ? '' : $tmp);?>
""/>
		</td>
	</tr>
	
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
staging url<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[staging_url]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['staging_url'])===null||$tmp==='' ? '' : $tmp);?>
""/>
		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contact email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[email]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['email'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
"
			class="{email:true}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Use a valid email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		</td>
	</tr>
	</table>
	
	<hr />
	
	<table class="areaform">
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
creator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[creator]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['creator'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
"
			class="{required:true,minLength:1}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array(1=>'1')); $_block_repeat=true; echo smarty_block_t(array(1=>'1'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Creator is required (at least %1 alphanumerical char)<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(1=>'1'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		</td>
		
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
publisher<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[publisher]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['publisher'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
"
			class="{required:true,minLength:1}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array(1=>'1')); $_block_repeat=true; echo smarty_block_t(array(1=>'1'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Publisher is required (at least %1 alphanumerical char)<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(1=>'1'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		</td>
		
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
rights<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[rights]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['rights'])===null||$tmp==='' ? '' : $tmp), ENT_QUOTES, 'UTF-8', true));?>
"
			class="{required:true,minLength:1}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array(1=>'1')); $_block_repeat=true; echo smarty_block_t(array(1=>'1'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Rights is required (at least %1 alphanumerical char)<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(1=>'1'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		</td>
		
	</tr>
	<tr>
				<td> <label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
license<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label></td>
				<td>
					<select name="data[license]">
						<option value="">--</option>
						<?php  $_smarty_tpl->tpl_vars['lic'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lic']->_loop = false;
 $_smarty_tpl->tpl_vars['code'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->defaultLicenses; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lic']->key => $_smarty_tpl->tpl_vars['lic']->value){
$_smarty_tpl->tpl_vars['lic']->_loop = true;
 $_smarty_tpl->tpl_vars['code']->value = $_smarty_tpl->tpl_vars['lic']->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['object']->value['license']==$_smarty_tpl->tpl_vars['code']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lic']->value['title'];?>
</option>
						<?php } ?>
						<?php  $_smarty_tpl->tpl_vars['lic'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lic']->_loop = false;
 $_smarty_tpl->tpl_vars['code'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->cfgLicenses; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lic']->key => $_smarty_tpl->tpl_vars['lic']->value){
$_smarty_tpl->tpl_vars['lic']->_loop = true;
 $_smarty_tpl->tpl_vars['code']->value = $_smarty_tpl->tpl_vars['lic']->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['object']->value['license']==$_smarty_tpl->tpl_vars['code']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lic']->value['title'];?>
</option>
						<?php } ?>
					</select>
				</td>
			</tr>
	</table>

</div>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Statistics<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset id="statistics">
	<table class="areaform">
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Provider<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<select name="data[stats_provider]">
				<option value="GoogleAnalytics" <?php if ("GoogleAnalytics"==$_smarty_tpl->tpl_vars['object']->value['stats_provider']){?>selected="selected"<?php }?>>Google analytics</option>
				<option value="PWik" <?php if ("piwik"==$_smarty_tpl->tpl_vars['object']->value['stats_provider']){?>selected="selected"<?php }?>>PWik</option>
				<option value="" <?php if (empty($_smarty_tpl->tpl_vars['object']->value['stats_provider'])){?>selected="selected"<?php }?>>Nessuno</option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Provider URL<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[stats_provider_url]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['stats_provider_url'])===null||$tmp==='' ? '' : $tmp);?>
"/>
			<?php if (isset($_smarty_tpl->tpl_vars['object']->value['stats_provider_url'])){?>
			<a href="<?php echo $_smarty_tpl->tpl_vars['object']->value['stats_provider_url'];?>
" target="_blank">
			› access statistics
			</a>
			<?php }?>
		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="2">
			<textarea name="data[stats_code]" style="font-size:0.8em; color:gray; width:470px;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['stats_code'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
		</td>
	</tr>
	</table>
		
	</fieldset>


	<?php echo smarty_function_assign_associative(array('var'=>"params",'object'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? null : $tmp)),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_translations',$_smarty_tpl->tpl_vars['params']->value);?>


	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_custom_properties');?>

	
	<?php echo smarty_function_assign_associative(array('var'=>"params",'el'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value)===null||$tmp==='' ? null : $tmp),'recursion'=>true),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_permissions',$_smarty_tpl->tpl_vars['params']->value);?>

	
	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_versions');?>

<?php }} ?>