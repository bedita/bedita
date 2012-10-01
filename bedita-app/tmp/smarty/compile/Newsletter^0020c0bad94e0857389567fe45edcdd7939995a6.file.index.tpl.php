<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:38
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1437971500505349667b8361-35466495%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0020c0bad94e0857389567fe45edcdd7939995a6' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/index.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1437971500505349667b8361-35466495',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'view' => 0,
    'subscribedWeek' => 0,
    'subscribedMonth' => 0,
    'subscribedTotal' => 0,
    'html' => 0,
    'recentMsg' => 0,
    'msg' => 0,
    'conf' => 0,
    'sentThisMonth' => 0,
    'sentThisYear' => 0,
    'sentTotal' => 0,
    'queued' => 0,
    'templates' => 0,
    'temp' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053496715f4b5_14293280',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053496715f4b5_14293280')) {function content_5053496715f4b5_14293280($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?><script type="text/javascript">
	$(document).ready( function ()
	{
		$('.tab').BEtabstoggle();
	});
</script>

<style>
	.bordered {
		width:100%; 
		margin-bottom:10px;
	}

</style>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"index"), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"index",'fixed'=>true), 0);?>


<div class="head">
	
		<h1><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Overview<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h1>

</div> 

<div class="mainfull" style="padding-right:0px; margin-right:0px;">
	
<div class="mainhalf">
	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subscribers<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
		<ul class="bordered">
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subscribed this week<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['subscribedWeek']->value)===null||$tmp==='' ? 0 : $tmp);?>
</b></li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subscribed this month<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['subscribedMonth']->value)===null||$tmp==='' ? 0 : $tmp);?>
</b></li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Total Subscribers<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b class="evidence"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['subscribedTotal']->value)===null||$tmp==='' ? 0 : $tmp);?>
</b></li>
			<li>
				<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/mailGroups');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b> 
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/mailGroups');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Import<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b> 
			</li>
		</ul>

	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Newsletters <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
		<table class="bordered" border=0 style="margin-top:-5px; width:100%">
		<?php if (!empty($_smarty_tpl->tpl_vars['recentMsg']->value)){?>
			<tr>
				<th style="width:100%"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Sent on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			</tr>
			
			<?php  $_smarty_tpl->tpl_vars["msg"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["msg"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['recentMsg']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["msg"]->key => $_smarty_tpl->tpl_vars["msg"]->value){
$_smarty_tpl->tpl_vars["msg"]->_loop = true;
?>
			<tr>
				<td><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/viewMailMessage/');?>
<?php echo $_smarty_tpl->tpl_vars['msg']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['msg']->value['title'];?>
</a></td>
				<td style="white-space:nowrap">
				<?php if ($_smarty_tpl->tpl_vars['msg']->value['mail_status']=="sent"){?>
					<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['msg']->value['start_sending'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>

				<?php }else{ ?>
					<i><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
not yet sent<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</i>
				<?php }?>
				</td>
			</tr>
			<?php } ?>
			<tr>	
				<td colspan="2" style="border-bottom:0px;">
					<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/newsletters');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/viewMailMessage');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create new<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b>
				</td>
			</tr>
		<?php }else{ ?>
			<tr><td colspan="2" style="width:340px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No newsletters found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td></tr>
			<tr>	
				<td colspan="2" style="border-bottom:0px;">
					<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/viewMailMessage');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create new<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b>
				</td>
			</tr>
		<?php }?>

		</table>
		
</div>
	
<div class="mainhalf" style="margin-right:0px;">
	
	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoices<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
		<ul class="bordered">
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Newsletters sent this month<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['sentThisMonth']->value)===null||$tmp==='' ? 0 : $tmp);?>
 </b></li>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Newsletters sent this year<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['sentThisYear']->value)===null||$tmp==='' ? 0 : $tmp);?>
</b> </li>
			<li>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Total newsletters sent<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['sentTotal']->value)===null||$tmp==='' ? 0 : $tmp);?>
</b> 
				&nbsp; &nbsp; | &nbsp; &nbsp; 
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Queued<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <b class="evidence"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['queued']->value)===null||$tmp==='' ? 0 : $tmp);?>
</b> </li>		 
			<li>
				<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/invoices');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View invoices<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b>
			</li>
		</ul>
	
	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Templates<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
		<ul class="bordered">
		<?php  $_smarty_tpl->tpl_vars["temp"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["temp"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['templates']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["temp"]->key => $_smarty_tpl->tpl_vars["temp"]->value){
$_smarty_tpl->tpl_vars["temp"]->_loop = true;
?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/viewMailTemplate/');?>
<?php echo $_smarty_tpl->tpl_vars['temp']->value['BEObject']['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['temp']->value['BEObject']['title'];?>
</a></li>
		<?php }
if (!$_smarty_tpl->tpl_vars["temp"]->_loop) {
?>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No template available<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
		<?php } ?>
			<li>
				<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/templates');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b>
					&nbsp;&nbsp;|&nbsp;&nbsp;
				<b><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/viewMailTemplate');?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Create new<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></b> 
			</li>
		</ul>

</div>
</div>
<?php }} ?>