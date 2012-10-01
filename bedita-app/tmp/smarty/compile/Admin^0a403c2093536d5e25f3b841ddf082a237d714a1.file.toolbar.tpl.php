<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:21
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/toolbar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:126406829506312e58f83d9-43894899%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0a403c2093536d5e25f3b841ddf082a237d714a1' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/admin/inc/toolbar.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '126406829506312e58f83d9-43894899',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'paginator' => 0,
    'pagParams' => 0,
    'label_items' => 0,
    'tr' => 0,
    'label_page' => 0,
    'label_next' => 0,
    'optionsPagDisable' => 0,
    'label_prev' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_506312e5a6e9c6_08558928',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_506312e5a6e9c6_08558928')) {function content_506312e5a6e9c6_08558928($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><script type="text/javascript">
	var refreshUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->here;?>
";
	$(document).ready(function() { 
		$("#pageDim").bind("change", function() {
			if(refreshUrl.match(/admin$/)) {
				refreshUrl += "/systemEvents";
			}
			document.location = refreshUrl + "/limit:" + this.value;
		} );
	} );
</script>

	<?php echo smarty_function_assign_associative(array('var'=>"optionsPagDisable",'style'=>"display: inline;"),$_smarty_tpl);?>

	<?php $_smarty_tpl->tpl_vars["pagParams"] = new Smarty_variable($_smarty_tpl->tpl_vars['paginator']->value->params(), null, 0);?>
<table>
	<tr>
		<td>
		<span class="evidence"><?php echo $_smarty_tpl->tpl_vars['pagParams']->value['count'];?>
&nbsp;</span> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['label_items']->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		</li>
		<?php $_smarty_tpl->tpl_vars['label_page'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('page',true), null, 0);?>
		<td>
			<?php if ($_smarty_tpl->tpl_vars['paginator']->value->hasPrev()){?>
				<?php echo $_smarty_tpl->tpl_vars['paginator']->value->first($_smarty_tpl->tpl_vars['label_page']->value);?>

			<?php }else{ ?>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<?php }?> 
			<span class="evidence"> <?php echo $_smarty_tpl->tpl_vars['paginator']->value->current();?>
</span>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
			<span class="evidence"> 
			<?php if ($_smarty_tpl->tpl_vars['paginator']->value->hasNext()){?>
				<?php echo $_smarty_tpl->tpl_vars['paginator']->value->last($_smarty_tpl->tpl_vars['pagParams']->value['pageCount']);?>

			<?php }else{ ?>
				<?php echo $_smarty_tpl->tpl_vars['paginator']->value->current();?>

			<?php }?>
			</span>
		</td>
		<?php $_smarty_tpl->tpl_vars['label_next'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('next',true), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['label_prev'] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t('prev',true), null, 0);?>
		<td><?php echo $_smarty_tpl->tpl_vars['paginator']->value->next($_smarty_tpl->tpl_vars['label_next']->value,null,$_smarty_tpl->tpl_vars['label_next']->value,$_smarty_tpl->tpl_vars['optionsPagDisable']->value);?>
  <span class="evidence"> &nbsp;</span></td>
		<td><?php echo $_smarty_tpl->tpl_vars['paginator']->value->prev($_smarty_tpl->tpl_vars['label_prev']->value,null,$_smarty_tpl->tpl_vars['label_prev']->value,$_smarty_tpl->tpl_vars['optionsPagDisable']->value);?>
  <span class="evidence"> &nbsp;</span></td>
		<td>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Page size<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:
			<select name="dim" id="pageDim">
				<option value="5"<?php if ($_smarty_tpl->tpl_vars['pagParams']->value['options']['limit']==5){?> selected="selected"<?php }?>>5</option>
				<option value="10"<?php if ($_smarty_tpl->tpl_vars['pagParams']->value['options']['limit']==10){?> selected="selected"<?php }?>>10</option>
				<option value="20"<?php if ($_smarty_tpl->tpl_vars['pagParams']->value['options']['limit']==20){?> selected="selected"<?php }?>>20</option>
				<option value="50"<?php if ($_smarty_tpl->tpl_vars['pagParams']->value['options']['limit']==50){?> selected="selected"<?php }?>>50</option>
				<option value="100"<?php if ($_smarty_tpl->tpl_vars['pagParams']->value['options']['limit']==100){?> selected="selected"<?php }?>>100</option>
			</select>
		</td>
	</tr>
</table>
<?php }} ?>