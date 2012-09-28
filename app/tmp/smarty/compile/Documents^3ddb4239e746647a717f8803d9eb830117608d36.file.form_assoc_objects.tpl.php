<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_assoc_objects.tpl" */ ?>
<?php /*%%SmartyHeaderCode:903785914504dfd9bf36f53-78132964%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3ddb4239e746647a717f8803d9eb830117608d36' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_assoc_objects.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '903785914504dfd9bf36f53-78132964',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9c1bd043_32294450',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'object_type_id' => 0,
    'view' => 0,
    'availabeRelations' => 0,
    'rel' => 0,
    'relObjects' => 0,
    'params' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9c1bd043_32294450')) {function content_504dfd9c1bd043_32294450($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.disable.text.select",true);?>


<script type="text/javascript">
var urlAddObjToAss= "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/loadObjectToAssoc/');?>
<?php echo $_smarty_tpl->tpl_vars['object']->value['id'];?>
";
<!--

function relatedRefreshButton() {
	$("#relationContainer").find("input[name='details']").click(function() {
		location.href = $(this).attr("rel");
	});
	
	$("#relationContainer").find("input[name='remove']").click(function() {
		tableToReorder = $(this).parents("table");
		$(this).parents("tr").remove();
		tableToReorder.fixItemsPriority();
	});
}

function addObjToAssoc(url, postdata) {
	$("#loadingDownloadRel").show();
	$.post(url, postdata, function(html){
		$("#loadingDownloadRel").hide();
		$("#relationType_" + postdata.relation + " table:first").find("tr:last").after(html);
		$("#relationType_" + postdata.relation).fixItemsPriority();
		$("#relationContainer table").find("tbody").sortable("refresh");
		relatedRefreshButton();
	});
}

function commitUploadItemDownloadRel(IDs) {
	obj_sel = {};
	obj_sel.object_selected = "";
	for(var i=0 ; i < IDs.length ; i++) {
		obj_sel.object_selected += IDs[i] + ",";
	}
	obj_sel.relation = "download";
	addObjToAssoc(urlAddObjToAss, obj_sel);	
}

function showResponseDownloadRel(data) {
	if (data.UploadErrorMsg) {
		$("#loadingDownloadRel").hide();
		$("#ajaxUploadContainerDownloadRel").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
	} else {
		var tmp = new Array() ;
		var countFile = 0; 
		$.each(data, function(entryIndex, entry) {
			tmp[countFile++] = entry['fileId'];
		});

		commitUploadItemDownloadRel(tmp);
	}
	
	$("#ajaxUploadContainerDownloadRel").find("input[@type=text]").attr("value", "");
	$("#ajaxUploadContainerDownloadRel").find("input[@type=file]").attr("value", "");
	$("#ajaxUploadContainerDownloadRel").find("textarea").attr("value", "");
}

function resetErrorDownloadRel() {
	$("#ajaxUploadContainerDownloadRel").find("label").remove();
	$("#loadingDownloadRel").show();
}

$(document).ready(function() {
	$("#relationContainer table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority
	}).css("cursor","move");
	
	relatedRefreshButton();
	
	$("input[name='addIds']").click(function() {
		obj_sel = {};
		input_ids = $(this).siblings("input[name='list_object_id']");
		obj_sel.object_selected = input_ids.val();
		obj_sel.relation = $(this).siblings("input[name*='switch']").val();
		addObjToAssoc(urlAddObjToAss, obj_sel);
		input_ids.val("");
	});
	
	// manage enter key on search text to prevent default submit
	$("input[name='list_object_id']").keypress(function(event) {
		if (event.keyCode == 13 && $(this).val() != "") {
			event.preventDefault();
			obj_sel = {};
			obj_sel.object_selected = $(this).val();
			obj_sel.relation = $(this).siblings("input[name*='switch']").val();
			addObjToAssoc(urlAddObjToAss, obj_sel);
			$(this).val("");
		}
	});

	// upload ajax for download relation
	var optionsFormDownloadRel = {
		beforeSubmit:	resetErrorDownloadRel,
		success:		showResponseDownloadRel,  // post-submit callback  
		dataType:		'json',        // 'xml', 'script', or 'json' (expected server response type)
		url: "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/files/uploadAjax/DownloadRel');?>
"
	};

	$("#uploadFormDownloadRel").click(function() {
		$('#updateForm').ajaxSubmit(optionsFormDownloadRel);
		return false;
	});
	
});

$(function() {
    $('.disableSelection').disableTextSelect();
});

//-->
</script>


<?php echo $_smarty_tpl->tpl_vars['view']->value->set("object_type_id",$_smarty_tpl->tpl_vars['object_type_id']->value);?>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Relationships<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="frmAssocObject">
	
	<div id="loadingDownloadRel" class="loader" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Loading data<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"></div>
	
	<table class="htab">
	<tr>
	<?php  $_smarty_tpl->tpl_vars["rel"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["rel"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['availabeRelations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["rel"]->key => $_smarty_tpl->tpl_vars["rel"]->value){
$_smarty_tpl->tpl_vars["rel"]->_loop = true;
?>
		<td rel="relationType_<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
	<?php } ?>
	</tr>
	</table>


	<div class="htabcontainer" id="relationContainer">
	<?php  $_smarty_tpl->tpl_vars["rel"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["rel"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['availabeRelations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["rel"]->key => $_smarty_tpl->tpl_vars["rel"]->value){
$_smarty_tpl->tpl_vars["rel"]->_loop = true;
?>
	<div class="htabcontent" id="relationType_<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
">

		<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
][0][switch]" value="<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
" />				
		
		<table class="indexlist" style="width:100%; margin-bottom:10px;">
			<tbody class="disableSelection">
			<?php if (!empty($_smarty_tpl->tpl_vars['relObjects']->value[$_smarty_tpl->tpl_vars['rel']->value])){?>
				<?php echo smarty_function_assign_associative(array('var'=>"params",'objsRelated'=>$_smarty_tpl->tpl_vars['relObjects']->value[$_smarty_tpl->tpl_vars['rel']->value],'rel'=>$_smarty_tpl->tpl_vars['rel']->value),$_smarty_tpl);?>

				<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_assoc_object',$_smarty_tpl->tpl_vars['params']->value);?>

			<?php }else{ ?>
				<tr><td colspan="10"></td></tr>
			<?php }?>
			</tbody>
		</table>
		
		<input type="button" class="modalbutton" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 : <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select an item to associate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"
		rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/showObjects/');?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? 0 : $tmp);?>
/<?php echo $_smarty_tpl->tpl_vars['rel']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['object_type_id']->value;?>
" style="width:200px" 
		value="  <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
connect new items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
  " />
		
		<?php if ($_smarty_tpl->tpl_vars['rel']->value=="download"){?>
			<?php echo smarty_function_assign_associative(array('var'=>"params",'uploadIdSuffix'=>"DownloadRel"),$_smarty_tpl);?>

			<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_upload_multi',$_smarty_tpl->tpl_vars['params']->value);?>

		<?php }?>
		
		

		
	</div>
	<?php } ?>
	</div>


	
</fieldset><?php }} ?>