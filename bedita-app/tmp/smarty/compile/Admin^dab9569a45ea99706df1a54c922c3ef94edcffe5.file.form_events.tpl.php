<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:21
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/form_events.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1231349465506312e5a85129-79429033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dab9569a45ea99706df1a54c922c3ef94edcffe5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/form_events.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1231349465506312e5a85129-79429033',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tr' => 0,
    'label_date' => 0,
    'paginator' => 0,
    'label_level' => 0,
    'label_user' => 0,
    'label_msg' => 0,
    'label_context' => 0,
    'events' => 0,
    'e' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_506312e5c3b782_65937302',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e5c3b782_65937302')) {function content_506312e5c3b782_65937302($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System events<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="system_events">
<div>
<?php $_smarty_tpl->tpl_vars['label_date'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('date',true), null, 0);?>
<?php $_smarty_tpl->tpl_vars['label_level'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('level',true), null, 0);?>
<?php $_smarty_tpl->tpl_vars['label_user'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('User',true), null, 0);?>
<?php $_smarty_tpl->tpl_vars['label_msg'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('msg',true), null, 0);?>
<?php $_smarty_tpl->tpl_vars['label_context'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('context',true), null, 0);?>
<table class="indexlist">
	<tr>
		<th><?php echo $_smarty_tpl->tpl_vars['paginator']->value->sort($_smarty_tpl->tpl_vars['label_date']->value,'created');?>
</th>
		<th><?php echo $_smarty_tpl->tpl_vars['paginator']->value->sort($_smarty_tpl->tpl_vars['label_level']->value,'level');?>
</th>
		<th><?php echo $_smarty_tpl->tpl_vars['paginator']->value->sort($_smarty_tpl->tpl_vars['label_user']->value,'user');?>
</th>
		<th><?php echo $_smarty_tpl->tpl_vars['paginator']->value->sort($_smarty_tpl->tpl_vars['label_msg']->value,'msg');?>
</th>
		<th><?php echo $_smarty_tpl->tpl_vars['paginator']->value->sort($_smarty_tpl->tpl_vars['label_context']->value,'context');?>
</th>
	</tr>
	<?php  $_smarty_tpl->tpl_vars['e'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['e']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['events']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['e']->key => $_smarty_tpl->tpl_vars['e']->value){
$_smarty_tpl->tpl_vars['e']->_loop = true;
?>
	<tr>
		<td style="white-space:nowrap"><?php echo $_smarty_tpl->tpl_vars['e']->value['EventLog']['created'];?>
</td>
		<td class="<?php echo $_smarty_tpl->tpl_vars['e']->value['EventLog']['log_level'];?>
"><?php echo $_smarty_tpl->tpl_vars['e']->value['EventLog']['log_level'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['e']->value['EventLog']['userid'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['e']->value['EventLog']['msg'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['e']->value['EventLog']['context'];?>
</td>
	</tr>
	<?php } ?>
</table>

</div>
</fieldset><?php }} ?>