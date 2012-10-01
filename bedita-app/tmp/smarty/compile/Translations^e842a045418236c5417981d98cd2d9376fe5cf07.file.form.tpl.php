<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 16:53:07
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/translations/inc/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:194009131505344d3ec1495-20375921%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e842a045418236c5417981d98cd2d9376fe5cf07' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/translations/inc/form.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '194009131505344d3ec1495-20375921',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'object_translation' => 0,
    'object_master' => 0,
    'object_translated_lang' => 0,
    'conf' => 0,
    'val' => 0,
    'object_master_langs' => 0,
    'label' => 0,
    'image' => 0,
    'beEmbedMedia' => 0,
    'lang_text_index' => 0,
    'l1' => 0,
    'image_status' => 0,
    'image_title' => 0,
    'image_description' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_505344d4925415_72620859',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_505344d4925415_72620859')) {function content_505344d4925415_72620859($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>

<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/translations/save');?>
" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_translation']->value['id']['status'])===null||$tmp==='' ? '' : $tmp);?>
"/>
<input type="hidden" name="data[master_id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_master']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>

<div class="mainhalf">

	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset id="tproperties" rel="properties">
	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
translation to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
		<?php $_smarty_tpl->tpl_vars['object_translated_lang'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['object_translation']->value['lang'])===null||$tmp==='' ? '' : $tmp), null, 0);?>
		
		<?php if (empty($_smarty_tpl->tpl_vars['object_translated_lang']->value)){?>
			<select style="font-size:1.2em;" name="data[translation_lang]" id="main_lang">
			
				<?php  $_smarty_tpl->tpl_vars['label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['label']->_loop = false;
 $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->langOptions; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['label']->key => $_smarty_tpl->tpl_vars['label']->value){
$_smarty_tpl->tpl_vars['label']->_loop = true;
 $_smarty_tpl->tpl_vars['val']->value = $_smarty_tpl->tpl_vars['label']->key;
?>
					<?php if (!in_array($_smarty_tpl->tpl_vars['val']->value,$_smarty_tpl->tpl_vars['object_master_langs']->value)){?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['val']->value=="eng"){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</option>
					<?php }?>
				<?php } ?>
				
				<?php  $_smarty_tpl->tpl_vars['label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['label']->_loop = false;
 $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->langsIso; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['label']->key => $_smarty_tpl->tpl_vars['label']->value){
$_smarty_tpl->tpl_vars['label']->_loop = true;
 $_smarty_tpl->tpl_vars['val']->value = $_smarty_tpl->tpl_vars['label']->key;
?>
					<?php if (!in_array($_smarty_tpl->tpl_vars['val']->value,$_smarty_tpl->tpl_vars['object_master_langs']->value)){?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['val']->value=="eng"){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</option>
					<?php }?>
				<?php } ?>
			
			</select>
		
		<?php }else{ ?>
		
			<select style="font-size:1.2em;" name="data[translation_lang]" id="main_lang">
					<option value="<?php echo $_smarty_tpl->tpl_vars['object_translated_lang']->value;?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['conf']->value->langOptions[$_smarty_tpl->tpl_vars['object_translated_lang']->value];?>
</option>
			</select>
		
		<?php }?>
		
		<hr />
		
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
		<input type="radio" name="data[LangText][0][text]" <?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value)&&($_smarty_tpl->tpl_vars['object_translation']->value['status']=='on')){?>checked="checked" <?php }?>value="on"/>ON
		<input type="radio" name="data[LangText][0][text]" <?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value)&&($_smarty_tpl->tpl_vars['object_translation']->value['status']=='off')){?>checked="checked" <?php }?>value="off"/>OFF
		<input type="radio" name="data[LangText][0][text]" <?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value)&&($_smarty_tpl->tpl_vars['object_translation']->value['status']=='draft')){?>checked="checked" <?php }?>value="draft"/>DRAFT
		<input type="radio" name="data[LangText][0][text]" <?php if (empty($_smarty_tpl->tpl_vars['object_translation']->value)||($_smarty_tpl->tpl_vars['object_translation']->value['status']=='required')){?>checked="checked" <?php }?>value="required"/>TO DO
		<input type="hidden" name="data[LangText][0][name]" value="status"/>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value)&&!empty($_smarty_tpl->tpl_vars['object_translation']->value['id'])&&!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['status'])){?><input type="hidden" name="data[LangText][0][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['status'];?>
"/><?php }?>

	</fieldset>



	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset id="ttitle" rel="title">
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<input type="text" id="title" name="data[LangText][1][text]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['title'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['title'];?>
<?php }?>"/><br />
		<input type="hidden" name="data[LangText][1][name]" value="title"/>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['title'])){?><input type="hidden" name="data[LangText][1][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['title'];?>
"/><?php }?>

		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['public_name'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
public name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<input type="text" name="data[LangText][2][text]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['public_name'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['public_name'];?>
<?php }?>"/>
		<input type="hidden" name="data[LangText][2][name]" value="public_name"/>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['public_name'])){?><input type="hidden" name="data[LangText][2][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['public_name'];?>
"/><?php }?>
		<?php }?>

		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['description'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<textarea id="subtitle" style="height:30px" class="mceSimple" name="data[LangText][3][text]"><?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['description'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['description'];?>
<?php }?></textarea>
		<input type="hidden" name="data[LangText][3][name]" value="description"/>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['description'])){?><input type="hidden" name="data[LangText][3][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['description'];?>
"/><?php }?>
		<?php }?>
	</fieldset>

	<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['abstract'])||!empty($_smarty_tpl->tpl_vars['object_master']->value['body'])){?>
	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

	<fieldset id="tlong_desc_langs_container" rel="long_desc_langs_container">
		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['abstract'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
short text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<textarea name="data[LangText][4][text]" style="height:200px" class="mcet"><?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['abstract'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['abstract'];?>
<?php }?></textarea>
		<input type="hidden" name="data[LangText][4][name]" value="abstract"/>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['abstract'])){?><input type="hidden" name="data[LangText][4][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['abstract'];?>
"/><?php }?>
		<br />
		<?php }?>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['body'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
long text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<textarea name="data[LangText][5][text]" style="height:400px" class="mcet"><?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['body'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['body'];?>
<?php }?></textarea>
		<input type="hidden" name="data[LangText][5][name]" value="body"/>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['body'])){?><input type="hidden" name="data[LangText][5][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['body'];?>
"/><?php }?>
		<?php }?>
	</fieldset>
	<?php }?>


<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['relations']['attach'])&&$_smarty_tpl->tpl_vars['object_master']->value['object_type_id']!=$_smarty_tpl->tpl_vars['conf']->value->objectTypes['image']['id']&&$_smarty_tpl->tpl_vars['object_master']->value['object_type_id']!=$_smarty_tpl->tpl_vars['conf']->value->objectTypes['video']['id']&&$_smarty_tpl->tpl_vars['object_master']->value['object_type_id']!=$_smarty_tpl->tpl_vars['conf']->value->objectTypes['b_e_file']['id']){?>

	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
multimedia descriptions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset rel="multimedia">
		<table style="margin-left:-10px; margin-bottom:20px;" border="0" cellpadding="0" cellspacing="2">
		<?php $_smarty_tpl->tpl_vars['lang_text_index'] = new Smarty_variable(10, null, 0);?>
		<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['object_master']->value['relations']['attach']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
?>
		<tr>
			<td>
				<a href="<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['image']->value,array('presentation'=>'full','URLonly'=>true));?>
">
					<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['image']->value,array('presentation'=>'thumb','mode'=>'crop','width'=>100,'height'=>100),array('style'=>'width:100px; height:100px; border:5px solid white; margin-bottom:0px;'));?>

				</a>
			</td>
			<td>
				<?php $_smarty_tpl->tpl_vars['l1'] = new Smarty_variable($_smarty_tpl->tpl_vars['lang_text_index']->value++, null, 0);?>
				<?php $_smarty_tpl->tpl_vars['image_status'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['image']->value['LangText']['status'][$_smarty_tpl->tpl_vars['object_translated_lang']->value])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['image']->value['status'] : $tmp), null, 0);?>
				<input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][name]" value="status"/>
				<input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][object_id]" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id'];?>
"/>
				<input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][text]" value="<?php echo $_smarty_tpl->tpl_vars['image_status']->value;?>
"/>
				<?php if (!empty($_smarty_tpl->tpl_vars['image']->value['LangText'][$_smarty_tpl->tpl_vars['image']->value['id']][$_smarty_tpl->tpl_vars['object_translated_lang']->value]['status'])){?><input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][id]" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['LangText'][$_smarty_tpl->tpl_vars['image']->value['id']][$_smarty_tpl->tpl_vars['object_translated_lang']->value]['status'];?>
"/><?php }?>

				<?php $_smarty_tpl->tpl_vars['l1'] = new Smarty_variable($_smarty_tpl->tpl_vars['lang_text_index']->value++, null, 0);?>
				<?php $_smarty_tpl->tpl_vars['image_title'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['image']->value['LangText']['title'][$_smarty_tpl->tpl_vars['object_translated_lang']->value])===null||$tmp==='' ? '' : $tmp), null, 0);?>
				<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
				<input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][name]" value="title"/>
				<input type="text" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][text]" style="width:210px !important" value="<?php echo $_smarty_tpl->tpl_vars['image_title']->value;?>
" />
				<input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][object_id]" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id'];?>
"/>
				<?php if ($_smarty_tpl->tpl_vars['image']->value['LangText']){?><input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['image']->value['LangText'][$_smarty_tpl->tpl_vars['image']->value['id']][$_smarty_tpl->tpl_vars['object_translated_lang']->value]['title'])===null||$tmp==='' ? '' : $tmp);?>
"/><?php }?>
				
				<?php $_smarty_tpl->tpl_vars['l1'] = new Smarty_variable($_smarty_tpl->tpl_vars['lang_text_index']->value++, null, 0);?>
				<?php $_smarty_tpl->tpl_vars['image_description'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['image']->value['LangText']['description'][$_smarty_tpl->tpl_vars['object_translated_lang']->value])===null||$tmp==='' ? '' : $tmp), null, 0);?>
				<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
				<input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][name]" value="description"/>
				<textarea style="height:38px; width:210px !important" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][text]"><?php echo $_smarty_tpl->tpl_vars['image_description']->value;?>
</textarea>
				<input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][object_id]" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id'];?>
"/>
				<?php if (!empty($_smarty_tpl->tpl_vars['image']->value['LangText'])){?><input type="hidden" name="data[LangText][<?php echo $_smarty_tpl->tpl_vars['l1']->value;?>
][id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['image']->value['LangText'][$_smarty_tpl->tpl_vars['image']->value['id']][$_smarty_tpl->tpl_vars['object_translated_lang']->value]['description'])===null||$tmp==='' ? '' : $tmp);?>
"/><?php }?>
			
			</td>
		</tr>
		<?php } ?>
		</table>
	</fieldset>

<?php }?>

	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Advanced Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset rel="advancedproperties">
	<table class="bordered">
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
created on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['created_on'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object_translation']->value['created_on'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
<?php }else{ ?>-<?php }?>
				<input type="hidden" name="data[LangText][6][text]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['created_on'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['created_on'];?>
<?php }?>"/>
				<input type="hidden" name="data[LangText][6][name]" value="created_on"/>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['created_on'])){?><input type="hidden" name="data[LangText][6][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['created_on'];?>
"/><?php }?>
			</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last modified on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['modified_on'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object_translation']->value['modified_on'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
<?php }else{ ?>-<?php }?>
				<input type="hidden" name="data[LangText][7][text]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['modified_on'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['modified_on'];?>
<?php }?>"/>
				<input type="hidden" name="data[LangText][7][name]" value="modified_on"/>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['modified_on'])){?><input type="hidden" name="data[LangText][7][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['modified_on'];?>
"/><?php }?>
			</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
created by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['created_by'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['created_by'];?>
<?php }else{ ?>-<?php }?>
				<input type="hidden" name="data[LangText][8][text]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['created_by'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['created_by'];?>
<?php }?>"/>
				<input type="hidden" name="data[LangText][8][name]" value="created_by"/>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['created_by'])){?><input type="hidden" name="data[LangText][8][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['created_by'];?>
"/><?php }?>
			</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last modified by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['modified_by'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['modified_by'];?>
<?php }else{ ?>-<?php }?>
				<input type="hidden" name="data[LangText][9][text]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['modified_by'])){?><?php echo $_smarty_tpl->tpl_vars['object_translation']->value['modified_by'];?>
<?php }?>"/>
				<input type="hidden" name="data[LangText][9][name]" value="modified_by"/>
				<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['id']['modified_by'])){?><input type="hidden" name="data[LangText][9][id]" value="<?php echo $_smarty_tpl->tpl_vars['object_translation']->value['id']['modified_by'];?>
"/><?php }?>
			</td>
		</tr>
	</table>
	</fieldset>

</div>


<!-- ///////////////////////// -->

<div class="mainhalf disabled">
	
<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset rel="properties">
	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
master language<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
		<select disabled style="font-size:1.2em;" id="eventoLang">
			<option label="<?php echo $_smarty_tpl->tpl_vars['conf']->value->langOptions[$_smarty_tpl->tpl_vars['object_master']->value['lang']];?>
" value="<?php echo $_smarty_tpl->tpl_vars['object_master']->value['lang'];?>
"><?php echo $_smarty_tpl->tpl_vars['conf']->value->langOptions[$_smarty_tpl->tpl_vars['object_master']->value['lang']];?>
</option>
		</select>	
		<hr />
		<label>status</label>:
		<input disabled type="radio" <?php if (($_smarty_tpl->tpl_vars['object_master']->value['status']=='on')){?>checked="checked" <?php }?>value="on">ON
		<input disabled type="radio" <?php if (($_smarty_tpl->tpl_vars['object_master']->value['status']=='off')){?>checked="checked" <?php }?>value="off">OFF
		<input disabled type="radio" <?php if (($_smarty_tpl->tpl_vars['object_master']->value['status']=='draft')){?>checked="checked" <?php }?>value="draft">DRAFT
	</fieldset>


	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Original Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset rel="title">
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<input type="text" id="title_master" name="" value="<?php echo $_smarty_tpl->tpl_vars['object_master']->value['title'];?>
" readonly="readonly"/><br />

		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['public_name'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
public name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<input type="text" name="" value="<?php echo $_smarty_tpl->tpl_vars['object_master']->value['public_name'];?>
"/>
		<?php }?>

		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['description'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<textarea class="mceSimple" name=""><?php echo $_smarty_tpl->tpl_vars['object_master']->value['description'];?>
</textarea>
		<?php }?>
	</fieldset>

	<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['abstract'])||!empty($_smarty_tpl->tpl_vars['object_master']->value['body'])){?>
	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

	<fieldset rel="long_desc_langs_container">
		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['abstract'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
short text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<textarea name="" style="height:200px" class="mcet"><?php echo $_smarty_tpl->tpl_vars['object_master']->value['abstract'];?>
</textarea>
		<br />
		<?php }?>
		<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['body'])){?>
		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
long text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label><br />
		<textarea name="" style="height:400px" class="mcet"><?php echo $_smarty_tpl->tpl_vars['object_master']->value['body'];?>
</textarea>
		<?php }?>
	</fieldset>
	<?php }?>


<?php if (!empty($_smarty_tpl->tpl_vars['object_master']->value['relations']['attach'])&&$_smarty_tpl->tpl_vars['object_master']->value['object_type_id']!=$_smarty_tpl->tpl_vars['conf']->value->objectTypes['image']['id']&&$_smarty_tpl->tpl_vars['object_master']->value['object_type_id']!=$_smarty_tpl->tpl_vars['conf']->value->objectTypes['video']['id']&&$_smarty_tpl->tpl_vars['object_master']->value['object_type_id']!=$_smarty_tpl->tpl_vars['conf']->value->objectTypes['b_e_file']['id']){?>
	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
multimedia descriptions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset rel="multimedia">
		
		<table style="margin-left:-10px; margin-bottom:20px;" border="0" cellpadding="0" cellspacing="2">
		<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['object_master']->value['relations']['attach']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
?>
		<tr>
			<td>
				<a href="<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['image']->value,array('presentation'=>'full','URLonly'=>true));?>
">
					<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['image']->value,array('presentation'=>'thumb','mode'=>'crop','width'=>100,'height'=>100),array('style'=>'width:100px; height:100px; border:5px solid white; margin-bottom:0px;'));?>

				</a>
			</td>
			<td>
				<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
				<input type="text" style="width:210px !important" name="" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['title'];?>
" />
				<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
				<textarea style="height:38px; width:210px !important" name=""><?php echo $_smarty_tpl->tpl_vars['image']->value['description'];?>
</textarea>
			</td>
		</tr>
		<?php } ?>
		</table>
	</fieldset>
<?php }?>

	<div class="tab2"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Advanced Properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<fieldset rel="advancedproperties">

	<table class="bordered">
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
created on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object_master']->value['created'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last modified on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object_master']->value['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
created by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo $_smarty_tpl->tpl_vars['object_master']->value['UserCreated']['userid'];?>
</td>
		</tr>
		<tr>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last modified by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
			<td><?php echo $_smarty_tpl->tpl_vars['object_master']->value['UserModified']['userid'];?>
</td>
		</tr>
	</table>

	</fieldset>

</div>


</form>
<?php }} ?>