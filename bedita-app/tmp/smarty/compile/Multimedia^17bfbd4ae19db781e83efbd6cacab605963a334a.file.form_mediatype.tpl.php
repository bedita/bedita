<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/form_mediatype.tpl" */ ?>
<?php /*%%SmartyHeaderCode:666331540504ef6da3aa641-89065667%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '17bfbd4ae19db781e83efbd6cacab605963a334a' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/form_mediatype.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '666331540504ef6da3aa641-89065667',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6da411df2_68680485',
  'variables' => 
  array (
    'object' => 0,
    'conf' => 0,
    'media_type' => 0,
    'cat' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6da411df2_68680485')) {function content_504ef6da411df2_68680485($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<?php $_smarty_tpl->tpl_vars['cat'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['object']->value['Category'])===null||$tmp==='' ? '' : $tmp), null, 0);?>
<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Media type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<div id="mediatypes">
	
<ul class="inline">
	<?php  $_smarty_tpl->tpl_vars["media_type"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["media_type"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->mediaTypes; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["media_type"]->key => $_smarty_tpl->tpl_vars["media_type"]->value){
$_smarty_tpl->tpl_vars["media_type"]->_loop = true;
?>
		<li style="width:95px" class="ico_<?php echo $_smarty_tpl->tpl_vars['media_type']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['cat']->value==$_smarty_tpl->tpl_vars['media_type']->value){?>on<?php }?>">
		<?php echo $_smarty_tpl->tpl_vars['media_type']->value;?>
 <input type="radio" name="mediatype" value="<?php echo $_smarty_tpl->tpl_vars['media_type']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['cat']->value==$_smarty_tpl->tpl_vars['media_type']->value){?>checked="checked"<?php }?> />
		</li>
	<?php } ?>
</ul>

<br style="clear:both !important" />
	
</div>

<?php }} ?>