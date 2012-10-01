<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_notes.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1370148367504ef6db331559-67619080%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8fdd32168b62d9b81ba0ef3908249aec1ee42208' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_notes.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1370148367504ef6db331559-67619080',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6db3653d5_97954937',
  'variables' => 
  array (
    'title' => 0,
    'object' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6db3653d5_97954937')) {function content_504ef6db3653d5_97954937($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['title']->value)===null||$tmp==='' ? 'Note' : $tmp);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="notes">

	<textarea style="width:100%; margin-bottom:2px; height:30px" class="mceSimple" name="data[note]">
	<?php echo $_smarty_tpl->tpl_vars['object']->value['note'];?>
</textarea>

</fieldset>
<?php }} ?>