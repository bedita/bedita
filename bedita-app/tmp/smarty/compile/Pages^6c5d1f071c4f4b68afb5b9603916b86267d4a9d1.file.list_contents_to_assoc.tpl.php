<?php /* Smarty version Smarty-3.1.11, created on 2012-09-19 15:48:50
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/pages/list_contents_to_assoc.tpl" */ ?>
<?php /*%%SmartyHeaderCode:490758476504e1012277fc3-51074705%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6c5d1f071c4f4b68afb5b9603916b86267d4a9d1' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/pages/list_contents_to_assoc.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '490758476504e1012277fc3-51074705',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504e1012438354_60399197',
  'variables' => 
  array (
    'html' => 0,
    'objectsToAssoc' => 0,
    'objToAss' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e1012438354_60399197')) {function content_504e1012438354_60399197($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.tablesorter.min");?>


<script type="text/javascript">
<!--
$(document).ready(function() {

	$("#contents_nav a").click(function() {
		loadObjToAssoc($(this).attr("rel"));
	});

	 $("#objtable").tablesorter(); 	
	 $("#objtable thead TH").css("cursor","pointer"); 

});
//-->
</script>

<?php if (!empty($_smarty_tpl->tpl_vars['objectsToAssoc']->value['items'])){?>
	
	<table class="indexlist" id="objtable">
	<thead>
		<tr>
			<th></th>
			<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th style="text-align:center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th style="text-align:center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th style="text-align:center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
modified<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th style="text-align:center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
lang<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<th style="text-align:center">Id</th>
		</tr>
	</thead>
	<tbody>
		<?php  $_smarty_tpl->tpl_vars["objToAss"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["objToAss"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['objectsToAssoc']->value['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["objToAss"]->key => $_smarty_tpl->tpl_vars["objToAss"]->value){
$_smarty_tpl->tpl_vars["objToAss"]->_loop = true;
?>
		<tr>
			<td style="width:15px; vertical-alig:middle; padding:0px 0px 0px 10px;">
				<input type="checkbox" name="object_selected[]" class="objectCheck" value="<?php echo $_smarty_tpl->tpl_vars['objToAss']->value['id'];?>
"/>
			</td>
			<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['objToAss']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</td>
			<td style="padding:0px; width:10px;">
				<span class="listrecent <?php echo $_smarty_tpl->tpl_vars['objToAss']->value['moduleName'];?>
" style="margin:0px 0px 0px 10px">&nbsp;</span>
			</td>
			<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['objToAss']->value['status'];?>
</td>
			<td style="text-align:center"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['objToAss']->value['modified'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
</td>
			<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['objToAss']->value['lang'];?>
</td>
			<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['objToAss']->value['id'];?>
</td>
		</tr>
		<?php } ?>
	</tbody>
	</table>


	<div id="contents_nav" class="graced" 
	style="text-align:center; color:#333; font-size:1.1em;  margin:25px 0px 1px 0px; background-color:#FFF; padding: 5px 10px 10px 10px;">
		
		<?php echo $_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['size'];?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 | <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo $_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['page'];?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo $_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['pages'];?>
 

		<?php if ($_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['first']>0){?>
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="<?php echo $_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['first'];?>
" id="streamFirstPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
first page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
first<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
		<?php }?>			

		<?php if ($_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['prev']>0){?>
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="<?php echo $_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['prev'];?>
" id="streamPrevPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
previous page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
prev<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['next']>0){?>
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="<?php echo $_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['next'];?>
" id="streamNextPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
next page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
next<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
		<?php }?>
		
		<?php if ($_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['last']>0){?>
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="<?php echo $_smarty_tpl->tpl_vars['objectsToAssoc']->value['toolbar']['last'];?>
" id="streamLastPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
		<?php }?>
									
	</div>

<?php }else{ ?>
	<div style="background-color:#FFF; padding:20px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No item found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
<?php }?><?php }} ?>