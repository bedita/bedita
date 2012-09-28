<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:38
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/list_sections.tpl" */ ?>
<?php /*%%SmartyHeaderCode:53175733250535c510fba94-73558482%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2083a5a23b743dac59d61183c3c2f4827c97b137' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/list_sections.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '53175733250535c510fba94-73558482',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50535c513cf523_78222409',
  'variables' => 
  array (
    'html' => 0,
    'sections' => 0,
    's' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c513cf523_78222409')) {function content_50535c513cf523_78222409($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
if (!is_callable('smarty_block_bedev')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.bedev.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.disable.text.select",true);?>


<script type="text/javascript">
<!--

$(document).ready(function() {

	var startSecPriority = $("#areasections").find("input[name*='[priority]']:first").val();
	
	//$("#areasections").sortable ({
	$("#areasections table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: function() {
					$(this).fixItemsPriority(startSecPriority);
				}
	}).css("cursor","move");

});

    $(function() {
        $('.disableSelection').disableTextSelect();
    });
	

//-->
</script>

<div style="min-height:100px; margin-top:10px;">
<?php if (!empty($_smarty_tpl->tpl_vars['sections']->value)){?>

	<div id="areasections">
	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
		
		
		<?php  $_smarty_tpl->tpl_vars["s"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["s"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sections']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["s"]->key => $_smarty_tpl->tpl_vars["s"]->value){
$_smarty_tpl->tpl_vars["s"]->_loop = true;
?>
		
			<tr class="obj <?php echo $_smarty_tpl->tpl_vars['s']->value['status'];?>
">
				
				<td class="checklist">
					
					<?php if ($_smarty_tpl->tpl_vars['s']->value['menu']==0){?>
					<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
hidden from menu and canonical path<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconHidden.png" style="height:30px; vertical-align:top;">
					<?php }?>
					
					<?php if (!empty($_smarty_tpl->tpl_vars['s']->value['start_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['s']->value['start_date'],"%Y%m%d"))>(smarty_modifier_date_format(time(),"%Y%m%d"))){?>
					
						<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object scheduled in the future<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFuture.png" style="height:28px; vertical-align:top;">
					
					<?php }elseif(!empty($_smarty_tpl->tpl_vars['s']->value['end_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['s']->value['end_date'],"%Y%m%d"))<(smarty_modifier_date_format(time(),"%Y%m%d"))){?>
					
						<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object expired<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconPast.png" style="height:28px; vertical-align:top;">
					
					<?php }elseif((!empty($_smarty_tpl->tpl_vars['s']->value['start_date'])&&((smarty_modifier_date_format($_smarty_tpl->tpl_vars['s']->value['start_date'],"%Y%m%d"))==(smarty_modifier_date_format(time(),"%Y%m%d"))))||(!empty($_smarty_tpl->tpl_vars['s']->value['end_date'])&&((smarty_modifier_date_format($_smarty_tpl->tpl_vars['s']->value['end_date'],"%Y%m%d"))==(smarty_modifier_date_format(time(),"%Y%m%d"))))){?>
					
						<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
object scheduled today<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconToday.png" style="height:28px; vertical-align:top;">
		
					<?php }?>
					
					<?php if (!empty($_smarty_tpl->tpl_vars['s']->value['num_of_permission'])){?>
						<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
permissions set<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconLocked.png" style="height:28px; vertical-align:top;">
					<?php }?>
					
					<?php if ((empty($_smarty_tpl->tpl_vars['s']->value['fixed']))){?>
						<input style="margin-top:8px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
" />
					<?php }else{ ?>
						<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
fixed object<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFixed.png" style="margin-top:8px; height:12px;" />
					<?php }?>
				</td>
				
				<td style="width:25px">
					<input type="hidden" class="id" 	name="reorder[<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
][id]" value="<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
" />
					<input type="text" class="priority"	name="reorder[<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
][priority]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['s']->value['priority'])===null||$tmp==='' ? '' : $tmp);?>
" 
					style="width:25px"
					size="3" maxlength="3"/>
				</td>
				<td style="padding:0px; padding-top:7px; width:10px"><span class="listrecent areas" style="margin:0px"></span></td>
				<td>
					<?php echo smarty_modifier_truncate((($tmp = @$_smarty_tpl->tpl_vars['s']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp),"64","…",true);?>

					<div class="description" style="width:auto" id="desc_<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
">
						<?php echo preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['s']->value['description']);?>
 / id:<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
 / nickname: <?php echo $_smarty_tpl->tpl_vars['s']->value['nickname'];?>

					</div>
				</td>

			<td>
					<a href="javascript:void(0)" onclick="$('#desc_<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
').slideToggle(); $('.plusminus',this).toggleText('+','-')">
						<span class="plusminus">+</span></a>	
				</td>
				<td style="white-space:nowrap">
					<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['s']->value['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>

				</td>
				<td>
					<?php echo $_smarty_tpl->tpl_vars['s']->value['status'];?>

				</td>

				<td>
					<?php echo $_smarty_tpl->tpl_vars['s']->value['lang'];?>

				</td>
				
				<td><?php if ((($tmp = @$_smarty_tpl->tpl_vars['s']->value['num_of_editor_note'])===null||$tmp==='' ? '' : $tmp)){?><img src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconNotes.gif" alt="notes" /><?php }?></td>
				
				<td class="commands" style="white-space:nowrap">

					<input type="button" class="BEbutton golink" onClick="window.location.href = ($(this).attr('href'));" 
					href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
areas/view/<?php echo $_smarty_tpl->tpl_vars['s']->value['id'];?>
" name="details" value="››" />

					<?php if (!empty($_smarty_tpl->tpl_vars['s']->value['fixed'])){?>
						<img title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
fixed object<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconFixed.png" style="margin-left:10px; height:12px;" />
					<?php }?>

				</td>
			</tr>
		<?php } ?>	
			
		</tbody>
	</table>
	</div>		
	
<?php }else{ ?>
	<em style="padding:20px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
no sections<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</em>
<?php }?>


	
	<?php echo $_smarty_tpl->getSubTemplate ("inc/tools_commands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('type'=>"section"), 0);?>

	
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('bedev', array()); $_block_repeat=true; echo smarty_block_bedev(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<?php echo $_smarty_tpl->getSubTemplate ("inc/bulk_actions.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('type'=>"section"), 0);?>
	
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_bedev(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>




<?php }} ?>