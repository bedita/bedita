<?php /* Smarty version Smarty-3.1.11, created on 2012-09-19 15:48:50
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/pages/show_objects.tpl" */ ?>
<?php /*%%SmartyHeaderCode:771440039504e1011f314e8-16865462%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7018c31edcac6d4f17f345ba364c839bc2862258' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/pages/show_objects.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '771440039504e1011f314e8-16865462',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504e10121e1481_93760163',
  'variables' => 
  array (
    'html' => 0,
    'relation' => 0,
    'objectTypeIds' => 0,
    'type_id' => 0,
    'conf' => 0,
    'tree' => 0,
    'beTree' => 0,
    'val' => 0,
    'label' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e10121e1481_93760163')) {function content_504e10121e1481_93760163($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_capitalize')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.capitalize.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><script type="text/javascript">
<!--
var urlShowObj = "<?php echo $_smarty_tpl->tpl_vars['html']->value->here;?>
";

if (typeof urlAddObjToAss<?php echo smarty_modifier_capitalize((($tmp = @$_smarty_tpl->tpl_vars['relation']->value)===null||$tmp==='' ? 'norelation' : $tmp));?>
 == "string") {
	var urlToAdd = urlAddObjToAss<?php echo smarty_modifier_capitalize($_smarty_tpl->tpl_vars['relation']->value);?>

} else if (typeof urlAddObjToAss == "string") { 
	var urlToAdd = urlAddObjToAss;
} else {
	var urlToAdd = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/loadObjectToAssoc');?>
";
}

var relType = "<?php echo (($tmp = @$_smarty_tpl->tpl_vars['relation']->value)===null||$tmp==='' ? '' : $tmp);?>
";
var suffix = "<?php echo smarty_modifier_capitalize((($tmp = @$_smarty_tpl->tpl_vars['relation']->value)===null||$tmp==='' ? '' : $tmp));?>
";


function loadObjToAssoc(page) {
	$("#loadObjInModal").show();
	$("#assocObjContainer").empty().load(urlShowObj, 
			{
				"parent_id": $("#parent_id").val(),
				"objectType": $("#objectType").val(),
				"lang": $("#lang").val(),
				"search": $("#search").val(),
				"page": page
			},
			function() {
				$("#loadObjInModal").hide();
	});
}

$(document).ready(function() {


	$(".searchTrigger").click(function() {
		$(".search").toggle('fast');
	});
	
	
	$("#searchButton").click(function() {
		loadObjToAssoc(1);
	});
	
	
	$("#searchButton").click(function() {
		loadObjToAssoc(1);
	});
	
	$("#addButton").click(function() {
		obj_sel = { relation: relType};
		obj_sel.object_selected = "";
		
		$("#assocObjContainer :checked").each(function() {
			obj_sel.object_selected += $(this).val() + ","; 
		});
		
		if (obj_sel.object_selected != "") {
			
			$("#modal").hide();
			$("#modaloverlay").hide();
			
			// if addObjToAssoc + suffix is defined use it (i.e. addObjToAssocQuestion in questionnaires/form_list_questions.tpl)
			// else addObjToAssoc function has to be defined in other template (i.e. elements/form_assoc_objects.tpl)
			if (eval("typeof addObjToAssoc" + suffix) == 'function') {
				eval("addObjToAssoc" + suffix)(urlToAdd, obj_sel);
			} else {
				addObjToAssoc(urlToAdd, obj_sel);
			}
			
		}
	});

});

//-->
</script>

<div class="bodybg">

<div class="searchTrigger" style="
background:white url('<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/piumeno.gif') no-repeat left 2px; 
padding:5px 0px 5px 30px; margin-bottom:1px; font-weight:bold; cursor:pointer;">
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
</div>

<div class="search" style="display:none; padding:10px; border:0px solid red;">
	
	<table>
		<tr>
			<th><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
word<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label></th>
			<td><input type="text" name="search" id="search" value="" /></td>
			<th><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label></th>
			<td>
				<select name="objectType" id="objectType">
					<option value=""><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['type_id'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type_id']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['objectTypeIds']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type_id']->key => $_smarty_tpl->tpl_vars['type_id']->value){
$_smarty_tpl->tpl_vars['type_id']->_loop = true;
?>
						<?php if ($_smarty_tpl->tpl_vars['type_id']->value){?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['type_id']->value;?>
"><?php echo mb_strtolower($_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['type_id']->value]['name'], 'UTF-8');?>
</option>
						<?php }?>
					<?php } ?>
				</select>
			</td>
			<td rowspan="2">
				<input type="button" id="searchButton" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Find it<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 ">
			</td>
		</tr>
		<tr>
			<th><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label></th>
			<td>
				<select style="width:180px" name="parent_id" id="parent_id">
				<?php echo $_smarty_tpl->tpl_vars['beTree']->value->option($_smarty_tpl->tpl_vars['tree']->value);?>

				</select>
			</td>
			<th><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
language<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label></th>
			<td>
					<select name="lang" id="lang">
					<option value=""><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['label']->_loop = false;
 $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->langOptions; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['label']->key => $_smarty_tpl->tpl_vars['label']->value){
$_smarty_tpl->tpl_vars['label']->_loop = true;
 $_smarty_tpl->tpl_vars['val']->value = $_smarty_tpl->tpl_vars['label']->key;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</option>
					<?php } ?>
				</select>
			</td>
			
		</tr>
	</table>

</div>
	
	<div id="loadObjInModal" class="loader"><span></span></div>
	
	<div id="assocObjContainer">
		<?php echo $_smarty_tpl->getSubTemplate ("list_contents_to_assoc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div>

	<div class="modalcommands">
		
		<input type="button" id="addButton" style="width:300px" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 ">
	
	</div>
</div><?php }} ?>