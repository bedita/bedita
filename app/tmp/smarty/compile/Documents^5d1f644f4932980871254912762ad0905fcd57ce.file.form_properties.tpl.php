<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:48
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_properties.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1130699467504dfd9b344fe0-64933566%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d1f644f4932980871254912762ad0905fcd57ce' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_properties.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1130699467504dfd9b344fe0-64933566',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9b60e557_40434115',
  'variables' => 
  array (
    'object' => 0,
    'conf' => 0,
    'BEAuthUser' => 0,
    'publication' => 0,
    'val' => 0,
    'object_lang' => 0,
    'label' => 0,
    'comments' => 0,
    'moduleList' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9b60e557_40434115')) {function content_504dfd9b60e557_40434115($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_html_radios')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/function.html_radios.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="properties">			
			
<table class="bordered">
		
	<tr>

		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="4">
			<?php if ($_smarty_tpl->tpl_vars['object']->value['fixed']){?>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This object is fixed - some data is readonly<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				<input type="hidden" name="data[status]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['status'];?>
" />
			<?php }else{ ?>
				<?php echo smarty_function_html_radios(array('name'=>"data[status]",'options'=>$_smarty_tpl->tpl_vars['conf']->value->statusOptions,'selected'=>(($tmp = @$_smarty_tpl->tpl_vars['object']->value['status'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->defaultStatus : $tmp),'separator'=>"&nbsp;"),$_smarty_tpl);?>

			<?php }?>
			
			<?php if (in_array('administrator',$_smarty_tpl->tpl_vars['BEAuthUser']->value['groups'])){?>
				&nbsp;&nbsp;&nbsp; <b>fixed</b>:&nbsp;&nbsp;<input type="checkbox" name="data[fixed]" value="1" <?php if (!empty($_smarty_tpl->tpl_vars['object']->value['fixed'])){?>checked<?php }?> />
			<?php }else{ ?>
				<input type="hidden" name="data[fixed]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['fixed'];?>
" />
			<?php }?>
		</td>
	</tr>

			

	<?php if (!(isset($_smarty_tpl->tpl_vars['publication']->value))||$_smarty_tpl->tpl_vars['publication']->value){?>

	<tr>
		<td colspan="2">
			<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
scheduled from<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>&nbsp;
			
			
			<input size="10" type="text" style="vertical-align:middle"
			class="dateinput" name="data[start_date]" id="start"
			value="<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['start_date'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['start_date'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
<?php }?>" />
			&nbsp;
			
			<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: </label>&nbsp;
			
			<input size="10" type="text" 
			class="dateinput" name="data[end_date]" id="end"
			value="<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['end_date'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['end_date'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
<?php }?>" />

		</td>
	</tr>

	<?php }?>

	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
author<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[creator]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['creator'];?>
" />
			<input type="hidden" name="data[user_created]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['user_created'];?>
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
	
	<?php if (isset($_smarty_tpl->tpl_vars['comments']->value)){?>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
comments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="radio" name="data[comments]" value="off"<?php if (empty($_smarty_tpl->tpl_vars['object']->value['comments'])||$_smarty_tpl->tpl_vars['object']->value['comments']=='off'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
			<input type="radio" name="data[comments]" value="on"<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['comments'])&&$_smarty_tpl->tpl_vars['object']->value['comments']=='on'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Yes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<input type="radio" name="data[comments]" value="moderated"<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['comments'])&&$_smarty_tpl->tpl_vars['object']->value['comments']=='moderated'){?> checked<?php }?>/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Moderated<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			&nbsp;&nbsp;
			<?php if (isset($_smarty_tpl->tpl_vars['moduleList']->value['comments'])&&$_smarty_tpl->tpl_vars['moduleList']->value['comments']['status']=="on"){?>
				<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['num_of_comment'])){?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
comments/index/comment_object_id:<?php echo $_smarty_tpl->tpl_vars['object']->value['id'];?>
"><img style="vertical-align:middle" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconComments.gif" alt="comments" /> (<?php echo $_smarty_tpl->tpl_vars['object']->value['num_of_comment'];?>
) <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
view<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				<?php }?>
			<?php }?>
		</td>
	</tr>
	<?php }?>
	
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
duration in minutes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[duration]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['duration'])){?><?php echo $_smarty_tpl->tpl_vars['object']->value['duration']/60;?>
<?php }?>" />
		</td>
	</tr>
</table>
	
</fieldset>
<?php }} ?>