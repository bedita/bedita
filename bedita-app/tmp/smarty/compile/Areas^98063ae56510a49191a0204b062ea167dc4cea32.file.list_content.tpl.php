<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:37
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/list_content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:41478262650535c5070e5b8-13847846%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98063ae56510a49191a0204b062ea167dc4cea32' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/inc/list_content.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '41478262650535c5070e5b8-13847846',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50535c50944c83_85453683',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'priorityOrder' => 0,
    'beurl' => 0,
    'objects' => 0,
    'tr' => 0,
    'allLabel' => 0,
    'beToolbar' => 0,
    'view' => 0,
    'conf' => 0,
    'objectTypeId' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c50944c83_85453683')) {function content_50535c50944c83_85453683($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_block_bedev')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.bedev.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.disable.text.select",true);?>


<script type="text/javascript">
<!--
var urlAddObjToAss = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/loadObjectToAssoc');?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? 0 : $tmp);?>
/leafs/areas.inc.list_contents_for_section";
var priorityOrder = "<?php echo (($tmp = @$_smarty_tpl->tpl_vars['priorityOrder']->value)===null||$tmp==='' ? 'asc' : $tmp);?>
";
var pageUrl = "<?php echo $_smarty_tpl->tpl_vars['beurl']->value->getUrl('object_type_id');?>
";



function addObjToAssoc(url, postdata) {
	$.post(url, postdata, function(html){
		if(priorityOrder == 'asc') {
			var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
			$("#areacontent tr:last").after(html);
		} else {
			var startPriority = parseInt($("#areacontent").find("input[name*='[priority]']:first").val());
			var beforeInsert = parseInt($("#areacontent tr").size());
			$("#areacontent tr:first").before(html);
			var afterInsert = parseInt($("#areacontent tr").size());
			startPriority = startPriority + (afterInsert - beforeInsert);
		}

		if ($("#noContents"))
			$("#noContents").remove();
		$("#areacontent").fixItemsPriority(startPriority);
		$("#areacontent").sortable("refresh");
		$("#areacontent table").find("tbody").sortable("refresh");
		setRemoveActions();
	});
}

function setRemoveActions() {
	$("#areacontent").find("input[name='remove']").click(function() {
		var contentField = $("#contentsToRemove").val() + $(this).parents().parents().find("input[name*='[id]']").val() + ",";
		$("#contentsToRemove").val(contentField);
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
		
		if (priorityOrder == "desc" && $(this) != $("#areacontent").find("input[name*='[priority]']:first")) {
			startPriority--;
		}
		
		$(this).parents().parents("tr").remove();
		

		$("#areacontent").fixItemsPriority(startPriority);
	});
}

$(document).ready(function() {

	if ($("#areacontent").find("input[name*='[priority]']:first")) {
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
	} else {
		var startPriority = 1;
	}

	//$("#areacontent").sortable ({
	$("#areacontent table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: function() {
					if (priorityOrder == 'desc' && startPriority < $("#areacontent").find("input[name*='[priority]']:first").val()) {
						startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
					}
					$(this).fixItemsPriority(startPriority);
				}
	}).css("cursor","move");
	
	setRemoveActions();
	
	$(".newcontenthere").click(function(){
		var urltogo = $('.selectcontenthere').val();
		window.location.href = urltogo;
		return false;
	});
		
	$("#selObjectType").change(function() {
		var url = ($(this).val() != "")? pageUrl + "/object_type_id:" + $(this).val() : pageUrl;
		location.href = url;
	});
	
});


    $(function() {
        $('.disableSelection').disableTextSelect();
    });


//-->
</script>



<div style="min-height:120px; margin-top:10px;">

	<div id="areacontent">

	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
			<input type="hidden" name="contentsToRemove" id="contentsToRemove" value=""/>
			<?php echo $_smarty_tpl->getSubTemplate ("../inc/list_contents_for_section.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('objsRelated'=>$_smarty_tpl->tpl_vars['objects']->value), 0);?>

		</tbody>
	</table>
	
	</div>

			
	<div id="contents_nav_leafs" style="margin-top:10px; padding:10px 0px 10px 0px; overflow:hidden; border-bottom:1px solid gray" class="ignore">	
		<div style="padding-left:0px; float:left;">
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
show<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<?php $_smarty_tpl->tpl_vars["allLabel"] = new Smarty_variable($_smarty_tpl->tpl_vars['tr']->value->t("all",true), null, 0);?>
		<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changeDimSelect('selectTop',array(),array(5=>5,10=>10,20=>20,50=>50,100=>100,1000000=>$_smarty_tpl->tpl_vars['allLabel']->value));?>
 &nbsp;
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
item(s)<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
		</div>	
		<div style="padding-left:30px; float:left;">
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
content type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<select id="selObjectType">
			<option value=""<?php if (empty($_smarty_tpl->tpl_vars['view']->value->params['named']['object_type_id'])){?> selected="selected"<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			<?php  $_smarty_tpl->tpl_vars["objectTypeId"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["objectTypeId"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->objectTypes['leafs']['id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["objectTypeId"]->key => $_smarty_tpl->tpl_vars["objectTypeId"]->value){
$_smarty_tpl->tpl_vars["objectTypeId"]->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['objectTypeId']->value;?>
" class="<?php echo $_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['objectTypeId']->value]['module_name'];?>
" style="padding-left:5px"
						<?php if (!empty($_smarty_tpl->tpl_vars['view']->value->params['named']['object_type_id'])&&$_smarty_tpl->tpl_vars['view']->value->params['named']['object_type_id']==$_smarty_tpl->tpl_vars['objectTypeId']->value){?>selected="selected"<?php }?>> <?php echo $_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['objectTypeId']->value]['name'];?>
</option>
			<?php } ?>
		</select>
		</div>
		<div class="toolbar sans" style="text-align:right; padding-left:30px; float:right;">
			
			<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->first('page','','page');?>

			 	
				<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->current();?>
 
				&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;
			
				<?php if (($_smarty_tpl->tpl_vars['beToolbar']->value->pages())>0){?>
				<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->last($_smarty_tpl->tpl_vars['beToolbar']->value->pages(),'',$_smarty_tpl->tpl_vars['beToolbar']->value->pages());?>

				<?php }else{ ?>1<?php }?>
			
			<?php if (($_smarty_tpl->tpl_vars['beToolbar']->value->pages())>1){?>
			
				&nbsp;
				
				<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->prev('‹ prev','','‹ prev');?>

			
				&nbsp;
				
				<?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->next('next ›','','next ›');?>

			
			<?php }?>
			
		</div>

	</div>


	<br style="clear:both" />
		
	<?php echo $_smarty_tpl->getSubTemplate ("inc/tools_commands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('type'=>"all"), 0);?>


	<?php $_smarty_tpl->smarty->_tag_stack[] = array('bedev', array()); $_block_repeat=true; echo smarty_block_bedev(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<?php echo $_smarty_tpl->getSubTemplate ("inc/bulk_actions.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('type'=>"all"), 0);?>

	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_bedev(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>



</div>	
	<?php }} ?>