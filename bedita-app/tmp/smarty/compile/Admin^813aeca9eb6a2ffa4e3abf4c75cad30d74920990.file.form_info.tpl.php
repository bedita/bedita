<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:25
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/form_info.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1130136974506312e981cee2-94470108%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '813aeca9eb6a2ffa4e3abf4c75cad30d74920990' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/form_info.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1130136974506312e981cee2-94470108',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'view' => 0,
    'conf' => 0,
    'sys' => 0,
    'ext' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_506312e99407d2_34636433',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e99407d2_34636433')) {function content_506312e99407d2_34636433($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo $_smarty_tpl->tpl_vars['view']->value->element('texteditor');?>


<script type="text/javascript">

$(document).ready(function(){
	var v = $().jquery;
	$("#jquery-version").text(v);
	if(tinymce != undefined) {
		v = tinymce.majorVersion + "." + tinymce.minorVersion;
		$("#tinymce-version").text(v);
	}
});

//-->
</script>	


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="system_info">
	
<table class="indexlist">
	<tr>
		<th colspan="2" style="text-transform:uppercase"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Software<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	</tr>
	<tr>
		<td><label>BEdita</label></td>
		<td><?php echo $_smarty_tpl->tpl_vars['conf']->value->Bedita['version'];?>
</td>
	</tr>
	<tr>
		<td><label>CakePHP</label></td>
		<td><?php echo $_smarty_tpl->tpl_vars['conf']->value->version();?>
</td>
	</tr>
	<tr>
		<td><label>PHP</label></td>
		<td><?php echo $_smarty_tpl->tpl_vars['sys']->value['phpVersion'];?>
</td>
	<tr>
		<td><label>PHP extensions</label></td>
		<td style="width:480px"><?php  $_smarty_tpl->tpl_vars["ext"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["ext"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sys']->value['phpExtensions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["ext"]->key => $_smarty_tpl->tpl_vars["ext"]->value){
$_smarty_tpl->tpl_vars["ext"]->_loop = true;
?> <?php echo $_smarty_tpl->tpl_vars['ext']->value;?>
<?php } ?></td>
	</tr>
	<tr>
		<td><label><?php echo $_smarty_tpl->tpl_vars['sys']->value['db'];?>
</label></td>
		<td>server: <?php echo (($tmp = @$_smarty_tpl->tpl_vars['sys']->value['dbServer'])===null||$tmp==='' ? '?' : $tmp);?>
 - client: <?php echo (($tmp = @$_smarty_tpl->tpl_vars['sys']->value['dbClient'])===null||$tmp==='' ? '?' : $tmp);?>
 - host: <?php echo $_smarty_tpl->tpl_vars['sys']->value['dbHost'];?>
 - db: <?php echo $_smarty_tpl->tpl_vars['sys']->value['dbName'];?>
</td>
	</tr>
	<tr>
		<td><label>Smarty</label></td>
		<td><?php echo 'Smarty-3.1.12';?>
</td>
	<tr>
	<tr>
		<td><label>JQuery</label></td>
		<td><p id="jquery-version"></p></td>
	</tr>
<?php if (((($tmp = @$_smarty_tpl->tpl_vars['conf']->value->mce)===null||$tmp==='' ? true : $tmp))){?>
	<tr>
		<td><label>TinyMCE</label></td>
		<td><p id="tinymce-version"></p></td>
	</tr>
<?php }?>	
	<tr>
		<td><label>Operating System</label></td>
		<td><?php echo $_smarty_tpl->tpl_vars['sys']->value['osVersion'];?>
</td>
	</tr>

	<tr>
		<th colspan="2" style="text-transform:uppercase"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
URLs and paths<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	</tr>
	<tr>
		<td><label>Media files URL</label></td>
		<td><a href="<?php echo $_smarty_tpl->tpl_vars['conf']->value->mediaUrl;?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['conf']->value->mediaUrl;?>
</a></td>
	</tr>
	<tr>
		<td><label>Media files root path</label></td>
		<td><?php echo $_smarty_tpl->tpl_vars['conf']->value->mediaRoot;?>
</td>
	</tr>
	<tr>
		<td><label>BEdita URL</label></td>
		<td><a href="<?php echo $_smarty_tpl->tpl_vars['conf']->value->beditaUrl;?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['conf']->value->beditaUrl;?>
</a></td>
	</tr>
	<tr>
		<td><label>BEdita app path</label></td>
		<td><?php echo $_smarty_tpl->tpl_vars['sys']->value['beditaPath'];?>
</td>
	</tr>
	<tr>
		<td><label>CakePHP path</label></td>
		<td><?php echo $_smarty_tpl->tpl_vars['sys']->value['cakePath'];?>
</td>
	</tr>
</table>

</fieldset><?php }} ?>