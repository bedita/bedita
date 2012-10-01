<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_subscribers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14512724245053497167cc67-59585012%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ef6694efcac478d8786743f0e86d9547945152c2' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/list_subscribers.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14512724245053497167cc67-59585012',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'subscribers' => 0,
    'beToolbar' => 0,
    's' => 0,
    'conf' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_505349717bb309_37968188',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_505349717bb309_37968188')) {function content_505349717bb309_37968188($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>
<?php if (!empty($_smarty_tpl->tpl_vars['subscribers']->value)){?>
		
	<table class="indexlist">
	<tr id="orderSubscribers">
		<th></th>
		<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('newsletter_email','Email');?>
</th>
		<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('mail_html','Html');?>
</th>
		<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('mail_status','Status');?>
</th>
		<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('created','Inserted on');?>
</th>
		<th></th>
	</tr>
	
	<?php  $_smarty_tpl->tpl_vars["s"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["s"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['subscribers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["s"]->key => $_smarty_tpl->tpl_vars["s"]->value){
$_smarty_tpl->tpl_vars["s"]->_loop = true;
?>
	<tr>
		<td><input name="objects_selected[]" type="checkbox" class="objectCheck" value="<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
" /></td>
		<td><?php echo $_smarty_tpl->tpl_vars['s']->value['newsletter_email'];?>
</td>
		<td><?php if ($_smarty_tpl->tpl_vars['s']->value['mail_html']){?><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
yes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }else{ ?><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?></td>
		<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['s']->value['mail_status'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['s']->value['created'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
</td>
		<td><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/addressbook/view/');?>
<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
">› <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
details<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>
	</tr>
	<?php } ?>
	
	</table>
	<hr />

	<table class="graced" id="paginateSubscribers">
	<tr>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->first('page','','page');?>

			<span class="evidence"> <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->current();?>
 </span> 
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
			<span class="evidence"> <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->last($_smarty_tpl->tpl_vars['beToolbar']->value->pages(),'',$_smarty_tpl->tpl_vars['beToolbar']->value->pages());?>
 </span>
			&nbsp;
		</td>
		<td style="border:1px solid gray; border-top:0px; border-bottom:0px;"><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->next('next','','next');?>
  <span class="evidence"> &nbsp;</span></td>
		<td><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->prev('prev','','prev');?>
  <span class="evidence"> &nbsp;</span></td>
	</tr>
	</table>
	
<?php }else{ ?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No subscribers<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?><?php }} ?>