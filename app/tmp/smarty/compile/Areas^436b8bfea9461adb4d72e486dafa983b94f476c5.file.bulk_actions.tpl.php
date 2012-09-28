<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:38
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/bulk_actions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:72311606250535c50e32d48-61856853%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '436b8bfea9461adb4d72e486dafa983b94f476c5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/bulk_actions.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '72311606250535c50e32d48-61856853',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50535c510ee361_33941779',
  'variables' => 
  array (
    'conf' => 0,
    'tree' => 0,
    'view' => 0,
    'sectionSel' => 0,
    'type' => 0,
    'beTree' => 0,
    'pubSel' => 0,
    'filter_section_name' => 0,
    'categories' => 0,
    'named_arr' => 0,
    'key' => 0,
    'category' => 0,
    'filter_category_name' => 0,
    'filter_category_id' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c510ee361_33941779')) {function content_50535c510ee361_33941779($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_html_options')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/function.html_options.php';
?>
<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Bulk actions on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;<span class="selecteditems evidence"></span> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
selected records<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<div class="ignore">
	
	
		<label for="selectAll" style="padding-right:20px; float:left;">
			<input type="checkbox" class="selectAll" id="selectAll" /> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
(un)select all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		
		</label>
		

<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
change status to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: 	<select style="width:75px" id="newStatus" name="newStatus">
								<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['conf']->value->statusOptions),$_smarty_tpl);?>

							</select>
			<input id="changestatusSelected" type="button" value=" ok " class="opButton"/>
	<hr />
	
	<?php if (!empty($_smarty_tpl->tpl_vars['tree']->value)){?>
		<?php $_smarty_tpl->tpl_vars['named_arr'] = new Smarty_variable($_smarty_tpl->tpl_vars['view']->value->params['named'], null, 0);?>
		
		<?php if (empty($_smarty_tpl->tpl_vars['sectionSel']->value['id'])){?>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
copy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		
		<?php }elseif($_smarty_tpl->tpl_vars['type']->value=="section"){?>
		
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
move<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			
		<?php }else{ ?>
			<select id="areaSectionAssocOp" name="areaSectionAssocOp" style="width:75px">
				<option value="copy"> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
copy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 </option>
				<option value="move"> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
move<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 </option>
			</select>
		<?php }?>
		&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
to<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:  &nbsp;

		<select id="areaSectionAssoc" style="width:320px" class="areaSectionAssociation" name="data[destination]">
		<?php echo $_smarty_tpl->tpl_vars['beTree']->value->option($_smarty_tpl->tpl_vars['tree']->value);?>

		</select>

		<input type="hidden" name="data[source]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['sectionSel']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
" />
		<input id="assocObjects" type="button" value=" ok " />
		<hr />
		
		<?php if ($_smarty_tpl->tpl_vars['type']->value!="section"&&!empty($_smarty_tpl->tpl_vars['sectionSel']->value['id'])){?>
			<?php $_smarty_tpl->tpl_vars['filter_section_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['sectionSel']->value['id'], null, 0);?>
			<?php $_smarty_tpl->tpl_vars['filter_section_name'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['pubSel']->value['title'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['sectionSel']->value['title'] : $tmp), null, 0);?>
			<input id="removeFromAreaSection" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove selected from<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 '<?php echo $_smarty_tpl->tpl_vars['filter_section_name']->value;?>
'" class="opButton" />
			<hr/>
		<?php }?>
		
	<?php }?>

	<?php if (!empty($_smarty_tpl->tpl_vars['categories']->value)){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<select id="objCategoryAssoc" class="objCategoryAssociation" name="data[category]">
		<option value="">--</option>
		<?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value){
$_smarty_tpl->tpl_vars['category']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['category']->key;
?>
		<?php if (!empty($_smarty_tpl->tpl_vars['named_arr']->value['category'])&&($_smarty_tpl->tpl_vars['key']->value==$_smarty_tpl->tpl_vars['named_arr']->value['category'])){?><?php $_smarty_tpl->tpl_vars['filter_category_name'] = new Smarty_variable($_smarty_tpl->tpl_vars['category']->value, null, 0);?><?php }?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['category']->value;?>
</option>
		<?php } ?>
		</select>
		<input id="assocObjectsCategory" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add association<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="opButton"/>
		
		<?php if (!empty($_smarty_tpl->tpl_vars['named_arr']->value['category'])){?>
			<hr />
			<?php $_smarty_tpl->tpl_vars['filter_category_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['named_arr']->value['category'], null, 0);?>
			<input id="disassocObjectsCategory" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove selected from category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 '<?php echo $_smarty_tpl->tpl_vars['filter_category_name']->value;?>
'" class="opButton" />
			<input id="filter_category" type="hidden" name="filter_category" value="<?php echo $_smarty_tpl->tpl_vars['filter_category_id']->value;?>
" />
		<?php }?>
	<?php }?>

</div><?php }} ?>