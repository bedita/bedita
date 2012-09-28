<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file_exif.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1628580785504ef6db126f18-13819464%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7a72f517764565314b3e2b94c07018518b01d857' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file_exif.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1628580785504ef6db126f18-13819464',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6db27c862_73804447',
  'variables' => 
  array (
    'object' => 0,
    'conf' => 0,
    'fileUrl' => 0,
    'imageInfo' => 0,
    'key' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6db27c862_73804447')) {function content_504ef6db27c862_73804447($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_function_image_info')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.image_info.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_dump')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.dump.php';
?>


<?php if ($_smarty_tpl->tpl_vars['object']->value['ObjectType']['name']=="image"){?>

		<?php if (strpos($_smarty_tpl->tpl_vars['object']->value['uri'],'/')===0){?>
			<?php echo smarty_function_assign_concat(array('var'=>"fileUrl",1=>$_smarty_tpl->tpl_vars['conf']->value->mediaRoot,2=>$_smarty_tpl->tpl_vars['object']->value['uri']),$_smarty_tpl);?>

		<?php }else{ ?>
			<?php $_smarty_tpl->tpl_vars["fileUrl"] = new Smarty_variable($_smarty_tpl->tpl_vars['object']->value['uri'], null, 0);?>
		<?php }?>
		<?php echo smarty_function_image_info(array('var'=>"imageInfo",'file'=>$_smarty_tpl->tpl_vars['fileUrl']->value),$_smarty_tpl);?>

		
<?php if (isset($_smarty_tpl->tpl_vars['imageInfo']->value)){?>
	<?php if ($_smarty_tpl->tpl_vars['imageInfo']->value['hrtype']=="JPG"){?>
<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Exif - Main Data<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="exifdata">

	<div style="line-height: 1.4em;">
	<?php if ($_smarty_tpl->tpl_vars['imageInfo']->value['exif']['main']){?>
		<?php  $_smarty_tpl->tpl_vars["value"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["value"]->_loop = false;
 $_smarty_tpl->tpl_vars["key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['imageInfo']->value['exif']['main']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["value"]->key => $_smarty_tpl->tpl_vars["value"]->value){
$_smarty_tpl->tpl_vars["value"]->_loop = true;
 $_smarty_tpl->tpl_vars["key"]->value = $_smarty_tpl->tpl_vars["value"]->key;
?>
		<span class="label"><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
</span>: <?php echo $_smarty_tpl->tpl_vars['value']->value;?>
<br />
		<?php } ?>
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['imageInfo']->value['exif']['XMP']){?>
		<h2 style="margin-top: 10px;">XMP (Adobe) data</h2>
		<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['name'] = 'XMP';
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['imageInfo']->value['exif']['XMP']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['XMP']['total']);
?>
		<?php if ($_smarty_tpl->tpl_vars['imageInfo']->value['exif']['XMP'][$_smarty_tpl->getVariable('smarty')->value['section']['XMP']['index']]['value']){?>
		<span class="label"><?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['exif']['XMP'][$_smarty_tpl->getVariable('smarty')->value['section']['XMP']['index']]['item'];?>
</span>: <?php echo $_smarty_tpl->tpl_vars['imageInfo']->value['exif']['XMP'][$_smarty_tpl->getVariable('smarty')->value['section']['XMP']['index']]['value'];?>
<br />
		<?php }?>
		<?php endfor; endif; ?>
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['imageInfo']->value['exif']['GPS']){?>
		<h2 style="margin-top: 10px;">GPS data</h2>
		<?php echo smarty_function_dump(array('var'=>$_smarty_tpl->tpl_vars['imageInfo']->value['exif']['GPS']),$_smarty_tpl);?>

	<?php }?>
	


	<?php if (!$_smarty_tpl->tpl_vars['imageInfo']->value['exif']['main']&&!$_smarty_tpl->tpl_vars['imageInfo']->value['exif']['XMP']&&!$_smarty_tpl->tpl_vars['imageInfo']->value['exif']['GPS']){?>
	EXIF records are empty.
	<?php }?>
	</div>

</fieldset>

	<?php }?>
<?php }?>
<?php }?><?php }} ?>