<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:48
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_textbody.tpl" */ ?>
<?php /*%%SmartyHeaderCode:823089590504dfd98b98d62-37694845%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '24579b717c2578ded915aa6ebe9fdd2a5efbd5fb' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_textbody.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '823089590504dfd98b98d62-37694845',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd98c5a121_62080638',
  'variables' => 
  array (
    'view' => 0,
    'addshorttext' => 0,
    'object' => 0,
    'height' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd98c5a121_62080638')) {function content_504dfd98c5a121_62080638($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<?php echo $_smarty_tpl->tpl_vars['view']->value->element('texteditor');?>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="long_desc_langs_container">

<?php if ((!empty($_smarty_tpl->tpl_vars['addshorttext']->value))||(!empty($_smarty_tpl->tpl_vars['object']->value['abstract']))){?>

		<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
short text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
		<textarea name="data[abstract]" style="height:200px" class="mce abstract"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['abstract'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
		
		<label for="body"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
long text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>

<?php }?>	
		<!-- per il drag&drop degli oggetti multimediali-->
		<div id="bodyDropTarget" class="dropTarget">
			<div class='dropSubTarget allowed' rel='placeref' data-attributes='{"class": "placeholder placeref","target":"modal"}' data-options='{"type": "append","object": "img"}'>
				<p>Rilascia qui per inserire come placeref</p>
			</div>
			<div class='dropSubTarget allowed' rel='placeholder' data-attributes='{"class": "placeholder","target":"modal"}' data-options='{"type": "append", "object": "img"}'>
				<p>Rilascia qui per inserire come placeholder</p>
			</div>
			<div class='dropSubTarget allowed' rel='simplelink' data-attributes='{"class": "simplelink","target":"modal"}' data-options='{"type": "wrap","selection":"required", "object": "a"}'>
				<p>Rilascia qui per inserire come link semplice</p>
			</div>
			<div class="dropSubTarget denied">
				<p>Seleziona prima qualcosa nell'editor</p>
			</div>
		</div>

		<textarea name="data[body]" style="height:<?php echo (($tmp = @$_smarty_tpl->tpl_vars['height']->value)===null||$tmp==='' ? 200 : $tmp);?>
px" class="mce body"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['body'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>

</fieldset><?php }} ?>