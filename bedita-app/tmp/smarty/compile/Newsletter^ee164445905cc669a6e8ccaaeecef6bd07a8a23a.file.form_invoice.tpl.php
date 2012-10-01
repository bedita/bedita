<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:13:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_invoice.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13760515025053497e3d62e2-33089248%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ee164445905cc669a6e8ccaaeecef6bd07a8a23a' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_invoice.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13760515025053497e3d62e2-33089248',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'conf' => 0,
    'groupsByArea' => 0,
    'pub' => 0,
    'groups' => 0,
    'group' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497e59a616_32765059',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497e59a616_32765059')) {function content_5053497e59a616_32765059($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?><script type="text/javascript">
var sendNewsletterUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/sendNewsletter');?>
";
var testNewsletterUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/testNewsletter');?>
";

$(document).ready(function() {
	$("#sendNewsletter").click(function() {
		$("#updateForm").attr("action", sendNewsletterUrl).submit();
	});
	
	$("#testNewsletter").click(function() {
		to = prompt("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Send email to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
		$("#updateForm").attr("action", testNewsletterUrl + "/" + to);
		$("#updateForm").submit();
	});
});

</script>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoice<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="invoice">			

<table class="bordered" style="width:100%">

	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
start<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
to recipients<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	</tr>
	<tr>
		<td style="vertical-align:middle">
			<input size=10 <?php if (($_smarty_tpl->tpl_vars['object']->value['mail_status']=="sent")){?>disabled=1<?php }?> type="text" class="dateinput" name="data[start_sending]" id="eventStart" value="<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['start_sending'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['start_sending'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
<?php }?>"/>
			<input size=5 <?php if (($_smarty_tpl->tpl_vars['object']->value['mail_status']=="sent")){?>disabled=1<?php }?> type="text" id="timeStart" name="data[start_sending_time]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['start_sending'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['start_sending'],"%H:%M");?>
<?php }?>" />

		</td>
		<td>
		<?php if (!empty($_smarty_tpl->tpl_vars['groupsByArea']->value)){?>
			<?php  $_smarty_tpl->tpl_vars["groups"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["groups"]->_loop = false;
 $_smarty_tpl->tpl_vars["pub"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['groupsByArea']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["groups"]->key => $_smarty_tpl->tpl_vars["groups"]->value){
$_smarty_tpl->tpl_vars["groups"]->_loop = true;
 $_smarty_tpl->tpl_vars["pub"]->value = $_smarty_tpl->tpl_vars["groups"]->key;
?>
				<ul>
					<li style="padding:2px;">
						<b><?php echo mb_strtoupper($_smarty_tpl->tpl_vars['pub']->value, 'UTF-8');?>
</b>
						<ul style="margin:0px">
						<?php  $_smarty_tpl->tpl_vars["group"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["group"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["group"]->key => $_smarty_tpl->tpl_vars["group"]->value){
$_smarty_tpl->tpl_vars["group"]->_loop = true;
?>
							<li style="padding:2px;">
							<input type="checkbox" 
							<?php if (($_smarty_tpl->tpl_vars['object']->value['mail_status']=="sent")){?>disabled=1<?php }?>
							name="data[MailGroup][]" value="<?php echo $_smarty_tpl->tpl_vars['group']->value['id'];?>
"<?php if (!empty($_smarty_tpl->tpl_vars['group']->value['MailMessage'])){?> checked<?php }?>/> <?php echo $_smarty_tpl->tpl_vars['group']->value['group_name'];?>

							</li>
						<?php } ?>
						</ul>
					</li>
				</ul>
			<?php } ?>
		<?php }?>
		</td>
		
		<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['mail_status'])&&$_smarty_tpl->tpl_vars['object']->value['mail_status']=="injob"){?>
			<td style="color:red; text-decoration: blink;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
in job<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<?php }else{ ?>
			<td class="info"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_status'])===null||$tmp==='' ? '' : $tmp);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<?php }?>
		
	</tr>
</table>
	<div class="modalcommands newsletter">
		<input type="button" id="testNewsletter" value="  test newsletter  " <?php if (!((($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? false : $tmp))){?>disabled="disabled"<?php }?>/> 
		<?php if ((empty($_smarty_tpl->tpl_vars['object']->value)||($_smarty_tpl->tpl_vars['object']->value['mail_status']!='sent'&&$_smarty_tpl->tpl_vars['object']->value['mail_status']!='injob'))){?>
		&nbsp;&nbsp;
		<?php if (($_smarty_tpl->tpl_vars['object']->value['mail_status']=="sent")){?>
			<p style="color:#FFF; padding:4px">
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Newsletter sent. To schedule another invoice, please clone this object.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</p>
		<?php }else{ ?>
			<input type="button" id="sendNewsletter" value="  SAVE & QUEUE newsletter  " />
		<?php }?>
		
		<?php }?>
	</div>
	
</fieldset>

<?php }} ?>