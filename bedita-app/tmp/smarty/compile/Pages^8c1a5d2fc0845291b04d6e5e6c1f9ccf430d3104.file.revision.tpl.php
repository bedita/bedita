<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:57:18
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/pages/revision.tpl" */ ?>
<?php /*%%SmartyHeaderCode:888182248504f190eebafa1-81662925%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8c1a5d2fc0845291b04d6e5e6c1f9ccf430d3104' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/pages/revision.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '888182248504f190eebafa1-81662925',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'version' => 0,
    'totRevision' => 0,
    'user' => 0,
    'conf' => 0,
    'diff' => 0,
    'key' => 0,
    'moduleName' => 0,
    'tr' => 0,
    'revision' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f190f09d060_57747039',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f190f09d060_57747039')) {function content_504f190f09d060_57747039($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->css('bedita4Modal');?>


<script>	

	$("#restoreselected").click(function (){
		
		$(".version input:checked").each(function(){
			
			// select appropriate target and source value
			var fieldid = $(this).val();
			var content = $(".revision#"+fieldid+"").html();
			var target = $("#updateForm *[name=data["+fieldid+"]]");
			var bgColor = "#ffccff";
			
			// change target content
			target.val(content).css("backgroundColor",bgColor).parents("fieldset").prev(".tab").BEtabsopen();
			
			if (target.hasClass("mce") || target.hasClass("mceSimple")) {
				var tinyMceInstance = tinyMCE.get("data["+fieldid+"]");
				if (tinyMceInstance == undefined) {
					tinyMceInstance = tinyMCE.get(fieldid);
				}
				if (tinyMceInstance != undefined) {
					tinyMceInstance.setContent(content);
					tinyMceInstance.getBody().style.backgroundColor = bgColor;
				}
			}
			
			//set page on to save/confirm on leave status
			$(".secondacolonna .modules label").addClass("save").attr("title","unsaved object");

			/*
			// da fare*******
			// 
			// i campi select e radio..
			// testare sulel card
			*/

		}); 
		
		$(".close").click();
	
	});
	
</script>

<div>
	<table class="version bordered">
	<thead>
		<tr>
			<td colspan=5>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <b><?php echo $_smarty_tpl->tpl_vars['version']->value['revision'];?>
</b> / <b><?php echo $_smarty_tpl->tpl_vars['totRevision']->value;?>
</b>, 
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
created by<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['user']->value['realname'])===null||$tmp==='' ? '' : $tmp);?>
 [<?php echo (($tmp = @$_smarty_tpl->tpl_vars['user']->value['userid'])===null||$tmp==='' ? '' : $tmp);?>
]</b>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span class="evidence"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['version']->value['created'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
</span>
				
				<!-- <input type="button" class="BEbutton" id="restoreall" style="margin-left:10px" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
restore all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" /> -->
				<input type="button" class="BEbutton" id="restoreselected" style="margin-left:10px" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
restore selected<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" /> 
			</td>
		</tr>
	</thead>
	<tbody>
	<?php  $_smarty_tpl->tpl_vars['xdiff'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['xdiff']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['diff']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['xdiff']->key => $_smarty_tpl->tpl_vars['xdiff']->value){
$_smarty_tpl->tpl_vars['xdiff']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['xdiff']->key;
?>
		<tr>
			<td style="width:20px"><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" /></td>
			<th nowrap><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['tr']->value->moduleField($_smarty_tpl->tpl_vars['moduleName']->value,$_smarty_tpl->tpl_vars['key']->value);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			<td class="revision" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['revision']->value[$_smarty_tpl->tpl_vars['key']->value])===null||$tmp==='' ? '' : $tmp);?>
</td>
			
		</tr>
	<?php } ?>
	</tbody>
	</table>
</div>
<?php }} ?>