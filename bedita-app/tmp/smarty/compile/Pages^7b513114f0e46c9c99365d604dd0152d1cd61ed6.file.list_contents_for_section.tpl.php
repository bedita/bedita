<?php /* Smarty version Smarty-3.1.11, created on 2012-09-19 15:48:54
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/list_contents_for_section.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18628962875056f84e528b27-77490649%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b513114f0e46c9c99365d604dd0152d1cd61ed6' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/list_contents_for_section.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18628962875056f84e528b27-77490649',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5056f84e801436_44603274',
  'variables' => 
  array (
    'objsRelated' => 0,
    'c' => 0,
    'html' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5056f84e801436_44603274')) {function content_5056f84e801436_44603274($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
?>
<?php  $_smarty_tpl->tpl_vars["c"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["c"]->_loop = false;
 $_from = (($tmp = @$_smarty_tpl->tpl_vars['objsRelated']->value)===null||$tmp==='' ? '' : $tmp); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["c"]->key => $_smarty_tpl->tpl_vars["c"]->value){
$_smarty_tpl->tpl_vars["c"]->_loop = true;
?>
	<tr class="obj <?php echo $_smarty_tpl->tpl_vars['c']->value['status'];?>
">
		
		<td class="checklist">
			<?php if (!empty($_smarty_tpl->tpl_vars['c']->value['start_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['c']->value['start_date'],"%Y%m%d"))>(smarty_modifier_date_format(time(),"%Y%m%d"))){?>
			
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object scheduled in the future<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFuture.png" style="height:28px; vertical-align:top;">
			
			<?php }elseif(!empty($_smarty_tpl->tpl_vars['c']->value['end_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['c']->value['end_date'],"%Y%m%d"))<(smarty_modifier_date_format(time(),"%Y%m%d"))){?>
			
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object expired<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconPast.png" style="height:28px; vertical-align:top;">
			
			<?php }elseif((!empty($_smarty_tpl->tpl_vars['c']->value['start_date'])&&((smarty_modifier_date_format($_smarty_tpl->tpl_vars['c']->value['start_date'],"%Y%m%d"))==(smarty_modifier_date_format(time(),"%Y%m%d"))))||(!empty($_smarty_tpl->tpl_vars['c']->value['end_date'])&&((smarty_modifier_date_format($_smarty_tpl->tpl_vars['c']->value['end_date'],"%Y%m%d"))==(smarty_modifier_date_format(time(),"%Y%m%d"))))){?>
			
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object scheduled today<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconToday.png" style="height:28px; vertical-align:top;">

			<?php }?>
			
			<?php if (!empty($_smarty_tpl->tpl_vars['c']->value['num_of_permission'])){?>
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
permissions set<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconLocked.png" style="height:28px; vertical-align:top;">
			<?php }?>
			
			<?php if ((empty($_smarty_tpl->tpl_vars['c']->value['fixed']))){?>
				<input style="margin-top:8px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
" />
			<?php }else{ ?>
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
fixed object<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFixed.png" style="margin-top:8px; height:12px;" />
			<?php }?>


		</td>	
		
		<td style="width:25px">
			<input type="hidden" class="id" name="reorder[<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
][id]" value="<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
" />
			<input type="text" class="priority"	name="reorder[<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
][priority]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['c']->value['priority'])===null||$tmp==='' ? '' : $tmp);?>
" 
			style="width:25px"
			size="3" maxlength="3"/>
		</td>
		<td style="padding:0px; padding-top:7px; width:10px"><span title="<?php echo $_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['c']->value['object_type_id']]['module_name'];?>
" class="listrecent <?php echo $_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['c']->value['object_type_id']]['module_name'];?>
" style="margin:0px"></span></td>
		<td>
			<?php echo smarty_modifier_truncate((($tmp = @$_smarty_tpl->tpl_vars['c']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp),"64","…",true);?>

			<div class="description" style="width:auto" id="desc_<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
">
				<?php echo preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['c']->value['description']);?>
 / id:<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
 / nickname: <?php echo $_smarty_tpl->tpl_vars['c']->value['nickname'];?>

			</div>
		</td>
		<td>
			<a href="javascript:void(0)" onclick="$('#desc_<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
').slideToggle(); $('.plusminus',this).toggleText('+','-')">
				<span class="plusminus">+</span></a>	
		</td>
		<td style="white-space:nowrap">
			<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['c']->value['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>

		</td>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['c']->value['status'];?>

		</td>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['c']->value['lang'];?>

		</td>
		
		<td><?php if ((($tmp = @$_smarty_tpl->tpl_vars['c']->value['num_of_editor_note'])===null||$tmp==='' ? '' : $tmp)){?><img src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconNotes.gif" alt="notes" /><?php }?></td>
		
		<td class="commands" style="white-space:nowrap">
			<input type="button" class="BEbutton golink" onClick="window.open($(this).attr('href'));" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['c']->value['object_type_id']]['module_name'];?>
/view/<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
" name="details" value="››" />
			<?php if (!empty($_smarty_tpl->tpl_vars['c']->value['fixed'])){?>
				
				<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
fixed object<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFixed.png" style="margin-left:10px; height:12px;" />
				
			<?php }else{ ?>
				<input type="button" name="remove" value="x" />
			<?php }?>
		</td>
	</tr>
<?php }
if (!$_smarty_tpl->tpl_vars["c"]->_loop) {
?>

	<tr>
		<td><i>no items</i></td>
	</tr>

<?php } ?>
<?php }} ?>