<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:55
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1698048547504e1032dae572-26774832%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b3e711b4eb5d643efd8a97e226967c5d1426209a' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/index.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1698048547504e1032dae572-26774832',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e1032ecfd46_91406758',
  'variables' => 
  array (
    'html' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e1032ecfd46_91406758')) {function content_504e1032ecfd46_91406758($_smarty_tpl) {?><?php if (!is_callable('smarty_block_bedev')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.bedev.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.changealert",false);?>




<script type="text/javascript">
	
var urlGetObj		= '<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/get_item_form_by_id");?>
' ;
var containerItem = "#multimediaItems";

$(document).ready(function() {  
	var optionsForm = {
		beforeSubmit:	resetError,
		success:		showResponse,  // post-submit callback  
		dataType:		'json',        // 'xml', 'script', or 'json' (expected server response type)
		url: "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/files/uploadAjax');?>
"
	};

	$("#uploadForm").click(function() {
		$('#uploadAjaxMedia').ajaxSubmit(optionsForm);
		return false;
	});
});
			
function commitUploadItem(IDs) {

	for(var i=0 ; i < IDs.length ; i++)
	{
		var id = escape(IDs[i]) ;
		var emptyDiv = "<div id='item_" + id + "' class='multimediaitem itemBox gold'><\/div>";
		$(emptyDiv).load(
			urlGetObj, { 'id': id, 'relation':'attach', 'template':'/elements/file_item'}, function (responseText, textStatus, XMLHttpRequest)
			{
				$("#loading").hide();
				$(containerItem).append(this); 
			}
		)
	}	
}

function showResponse(data) {
	if (data.UploadErrorMsg) {
		$("#loading").hide();
		$("#ajaxUploadContainer").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
	} else {
		var tmp = new Array() ;
		var countFile = 0; 
		$.each(data, function(entryIndex, entry) {
			tmp[countFile++] = entry['fileId'];
		});

		commitUploadItem(tmp);
	}
	
	$("#ajaxUploadContainer").find("input[type=text]").attr("value", "");
	$("#ajaxUploadContainer").find("input[type=file]").attr("value", "");
	$("#ajaxUploadContainer").find("textarea").attr("value", "");
}

function resetError() {
	$("#ajaxUploadContainer").find("label").remove();
	$("#loading").show();
}

</script>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('fixed'=>true), 0);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('toolbar');?>


<div class="mainfull">

	<?php echo $_smarty_tpl->getSubTemplate ("./inc/list_streams.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('streamTitle'=>"multimedia"), 0);?>

	
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('bedev', array()); $_block_repeat=true; echo smarty_block_bedev(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_bedev(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	
	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add multiple items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	
	<div class="htabcontent">
		<div style="clear:both; margin:-20px 0px 20px -20px">
		
		<form id="uploadAjaxMedia" action="#" method="post" enctype="multipart/form-data">
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_upload_multi');?>

		</form>
		</div>		 

		<div id="loading" style="clear:both" class="multimediaitem itemBox small">&nbsp;</div>
		<div id="multimediaItems"></div>
			

	</div>
	
</div>

<?php }} ?>