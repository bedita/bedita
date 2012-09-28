<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:59
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_objects_newsletter.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1491371825053497b756a40-86037762%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e59054811bd5368b2074eb77112fed730ebd2ea1' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_objects_newsletter.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1491371825053497b756a40-86037762',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'beToolbar' => 0,
    'objects' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497ba422f2_55990126',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497ba422f2_55990126')) {function content_5053497ba422f2_55990126($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>
<script type="text/javascript">
<!--
var urlDelete = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('delete/');?>
" ;
var message = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete the item?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" ;
var messageSelected = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete selected items?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" ;
var URLBase = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('index/');?>
" ;
var urlChangeStatus = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('changeStatusObjects/');?>
";
var urlAddToAreaSection = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('addItemsToAreaSection/');?>
";

$(document).ready(function(){
	
	$("#deleteSelected").bind("click", function() {
		if(!confirm(message)) 
			return false ;	
		$("#formObject").attr("action", urlDelete) ;
		$("#formObject").submit() ;
	});
	
	
	$("#assocObjects").click( function() {
		$("#formObject").attr("action", urlAddToAreaSection) ;
		$("#formObject").submit() ;
	});
	
	$("#changestatusSelected").click( function() {
		$("#formObject").attr("action", urlChangeStatus) ;
		$("#formObject").submit() ;
	});
});

//-->
</script>	


	<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>


	<table class="indexlist">
	<?php $_smarty_tpl->_capture_stack[0][] = array("theader", null, null); ob_start(); ?>
		<tr>
			<th></th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('title','Title');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('id','id');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('status','Status');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('sent','last invoice');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('template','Template');?>
</th>	
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('lang','language');?>
</th>
		</tr>
	<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		
		<?php echo Smarty::$_smarty_vars['capture']['theader'];?>

		
		<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['name'] = "i";
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['objects']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total']);
?>
		
		<tr class="obj <?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['mail_status'];?>
">
			<td style="width:15px;">
			<?php if ((empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['fixed']))){?>
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
" />
			<?php }?>
			</td>
			<td><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('view/');?>
<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
"><?php echo (($tmp = @smarty_modifier_truncate($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'],64))===null||$tmp==='' ? "<i>[no title]</i>" : $tmp);?>
</a></td>
			<td><?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'];?>
</td>

			<?php if (!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['mail_status'])&&$_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['mail_status']=="injob"){?>
				<td style="color:red; text-decoration: blink;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
in job<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
			<?php }elseif(($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['mail_status']=="pending")){?>
				<td class="info"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['mail_status'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
			<?php }else{ ?>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['mail_status'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
			<?php }?>
					
			
			<td><?php if (!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['sent'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['sent'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
 <?php }?></td>
			
			<td>
			<?php if (!empty($_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['i']['index']]['relations']['template'])){?>
				<a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/viewtemplate/');?>
<?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['relations']['template'][0]['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['relations']['template'][0]['title'];?>
</a>
			<?php }?>
			</td>
			<td><?php echo $_smarty_tpl->tpl_vars['objects']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['lang'];?>
</td>
		</tr>
			
		<?php endfor; else: ?>
		
			<tr><td colspan="100" style="padding:30px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No items found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td></tr>
		
		<?php endif; ?>
		
<?php if (($_smarty_tpl->getVariable('smarty')->value['section']['i']['total'])>=10){?>
		
			<?php echo Smarty::$_smarty_vars['capture']['theader'];?>

			
<?php }?>


</table>


<br />
	
<?php if (!empty($_smarty_tpl->tpl_vars['objects']->value)){?>

<div style="white-space:nowrap">
	
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Go to page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changePageSelect('pagSelectBottom');?>
 
	&nbsp;&nbsp;&nbsp;
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Dimensions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changeDimSelect('selectTop');?>
 &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
(un)select all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>

	
</div>



<?php }?>

</form>

<br />
<br />
<br />
<br />
	
	



<?php }} ?>