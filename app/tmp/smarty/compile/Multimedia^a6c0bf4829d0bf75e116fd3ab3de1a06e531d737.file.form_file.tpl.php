<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file.tpl" */ ?>
<?php /*%%SmartyHeaderCode:976087197504ef6da41acc4-09550869%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a6c0bf4829d0bf75e116fd3ab3de1a06e531d737' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '976087197504ef6da41acc4-09550869',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6da822a95_96931411',
  'variables' => 
  array (
    'object' => 0,
    'conf' => 0,
    'fileUrl' => 0,
    'params' => 0,
    'beEmbedMedia' => 0,
    'htmlAttr' => 0,
    'htmlAttributes' => 0,
    'imageInfo' => 0,
    'uri' => 0,
    'html' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6da822a95_96931411')) {function content_504ef6da822a95_96931411($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_function_image_info')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.image_info.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_modifier_filesize')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/modifier.filesize.php';
?>


<?php if ((isset($_smarty_tpl->tpl_vars['object']->value))&&(!empty($_smarty_tpl->tpl_vars['object']->value['uri']))){?>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
File<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="multimediaitem" style="margin-left:-10px;">

<div class="multimediaiteminside">

<?php if (($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name']=="image")){?>

	<?php if (strpos($_smarty_tpl->tpl_vars['object']->value['uri'],'/')===0){?>
		<?php echo smarty_function_assign_concat(array('var'=>"fileUrl",1=>$_smarty_tpl->tpl_vars['conf']->value->mediaRoot,2=>$_smarty_tpl->tpl_vars['object']->value['uri']),$_smarty_tpl);?>

	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars["fileUrl"] = new Smarty_variable($_smarty_tpl->tpl_vars['object']->value['uri'], null, 0);?>
	<?php }?>
	<?php echo smarty_function_image_info(array('var'=>"imageInfo",'file'=>$_smarty_tpl->tpl_vars['fileUrl']->value),$_smarty_tpl);?>


	<?php echo smarty_function_assign_associative(array('var'=>"params",'width'=>500,'longside'=>false,'mode'=>"fill",'modeparam'=>"000000",'type'=>null,'upscale'=>false),$_smarty_tpl);?>


	<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['object']->value,$_smarty_tpl->tpl_vars['params']->value);?>


	
<?php }elseif(strtolower(($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name'])=="video")){?>

	<?php echo smarty_function_assign_associative(array('var'=>"params",'presentation'=>"full"),$_smarty_tpl);?>

	<?php echo smarty_function_assign_associative(array('var'=>"htmlAttr",'width'=>500,'height'=>345),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['object']->value,$_smarty_tpl->tpl_vars['params']->value,$_smarty_tpl->tpl_vars['htmlAttr']->value);?>

	
<?php }elseif(strtolower($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name'])=="audio"){?>

	<?php echo smarty_function_assign_associative(array('var'=>"htmlAttr",'id'=>"multimediaitemaudio"),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['object']->value,null,$_smarty_tpl->tpl_vars['htmlAttr']->value);?>

	
<?php }elseif(strtolower($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name'])=="application"){?>
	
	<?php echo smarty_function_assign_associative(array('var'=>"htmlAttributes",'id'=>"appContainer"),$_smarty_tpl);?>
 
	<?php echo smarty_function_assign_associative(array('var'=>"params",'presentation'=>"full"),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['object']->value,$_smarty_tpl->tpl_vars['params']->value,$_smarty_tpl->tpl_vars['htmlAttributes']->value);?>

	
<?php }else{ ?>
		
	<a href="<?php echo $_smarty_tpl->tpl_vars['conf']->value->mediaUrl;?>
<?php echo $_smarty_tpl->tpl_vars['object']->value['uri'];?>
" target="_blank">
		<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['object']->value);?>

	</a>


<?php }?>




<table class="bordered" style="margin:10px auto; width:95%; border:1px solid #999; clear:both">

	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
filename<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="3"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['name'])===null||$tmp==='' ? '' : $tmp);?>
</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
original filename<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="3"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['original_name'])===null||$tmp==='' ? '' : $tmp);?>
</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
mime type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['mime_type'])===null||$tmp==='' ? '' : $tmp);?>
</td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
filesize<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><?php echo smarty_modifier_filesize($_smarty_tpl->tpl_vars['object']->value['file_size']);?>
</td>
	</tr>

<?php if (strtolower($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name'])=="application"){?>
	
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Width<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" size="6" name="data[width]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['width'];?>
"/></td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Height<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" size="6" name="data[height]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['height'];?>
"/></td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="3"><input type="text" name="data[application_version]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['application_version'];?>
"/></td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Text direction<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="3">
			<select name="data[text_dir]">
				<option value=""></option>
				<option value="ltr" <?php if ($_smarty_tpl->tpl_vars['object']->value['text_dir']=='ltr'){?>selected="selected"<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
left to right<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
				<option value="rtl" <?php if ($_smarty_tpl->tpl_vars['object']->value['text_dir']=='rtl'){?>selected="selected"<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
right to left<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Text lang<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td colspan="3"><input type="text" name="data[text_lang]" value="<?php echo $_smarty_tpl->tpl_vars['object']->value['text_lang'];?>
"/></td>
	</tr>

<?php }?>


<?php if (($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name']=="image")){?>
	
	<tr>
		<th nowrap><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Human readable type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['hrtype'];?>
</td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Orientation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['orientation'];?>
</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Width<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['w'];?>
</td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Height<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['h'];?>
</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Bit depth<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th><td><?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['bits'];?>
</td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Channels<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th><td><?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['channels'];?>
</td>
	</tr>

<?php }?>
	
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Url<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <!-- <input type="button" onclick="$('#mediaurl').copy();" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
copy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" /> --> </th>
		<td colspan="3">

		<?php if ((substr($_smarty_tpl->tpl_vars['object']->value['uri'],0,7)=='http://')||(substr($_smarty_tpl->tpl_vars['object']->value['uri'],0,8)=='https://')){?>
			<?php $_smarty_tpl->tpl_vars["uri"] = new Smarty_variable($_smarty_tpl->tpl_vars['object']->value['uri'], null, 0);?>
		<?php }else{ ?>
			<?php echo smarty_function_assign_concat(array('var'=>"uri",1=>$_smarty_tpl->tpl_vars['conf']->value->mediaUrl,2=>$_smarty_tpl->tpl_vars['object']->value['uri']),$_smarty_tpl);?>

		<?php }?>
			<a target="_blank" id="mediaurl" href="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
">
				<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>

			</a>
		</td>
	</tr>
	<?php if (!empty($_smarty_tpl->tpl_vars['html']->value->params['isAjax'])){?>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['object']->value['id'];?>

		</td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Unique name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['object']->value['nickname'];?>

		</td>
	</tr>
	<?php }?>

	<?php if (($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name']!="image")){?>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
thumbnail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
		<td colspan="3">
		<input type="text" name="data[thumbnail]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['thumbnail'])===null||$tmp==='' ? '' : $tmp);?>
" style="width: 350px;"/>
		
		</td>
	</tr>
	<?php }?>

	</table>
		
</div>

</fieldset>


<?php }?>


<div class="tab"><h2>
	<?php if ((!isset($_smarty_tpl->tpl_vars['object']->value))||(empty($_smarty_tpl->tpl_vars['object']->value['uri']))){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Upload new file<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php }else{ ?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Change this file with another<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php }?>
	</h2></div>

<fieldset id="add">

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('upload_choices');?>


<table class="htab">
	<td rel="uploadItems"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
browse your disk<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
	<td rel="urlItems"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add by url<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
</table>


<div class="htabcontainer" id="addmultimediacontents">

	<div class="htabcontent" id="uploadItems">
		<input style="margin:20px; width:270px;" type="file" name="Filedata" />
	</div>
	
	
	<div class="htabcontent" id="urlItems">
		
		<table style="margin:20px;">
		<tr>
			<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Url<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
			<td><input type="text" style="width:270px;" name="data[url]" /></td>
		</tr>
		
		</table>
	</div>

</div>

</fieldset>
<?php }} ?>