<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_upload_multi.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2054335405504ef5e2614157-26711762%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '108eb2f90f6cc507cf7df138115b6aa9433f0100' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_upload_multi.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2054335405504ef5e2614157-26711762',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'uploadIdSuffix' => 0,
    'session' => 0,
    'params' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e273db11_37417471',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e273db11_37417471')) {function content_504ef5e273db11_37417471($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script('swfobject',false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script('jquery/jquery.uploadify.min',false);?>

<?php if (empty($_smarty_tpl->tpl_vars['uploadIdSuffix']->value)){?>
	<?php $_smarty_tpl->tpl_vars['uploadIdSuffix'] = new Smarty_variable('', null, 0);?>
<?php }?>

<script type="text/javascript">
<!--
var webroot = "<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
";
var multiUploadUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
files/upload";
var u_id = "<?php echo $_smarty_tpl->tpl_vars['session']->value->read("BEAuthUser.id");?>
";
var uploadIdSuffix = "<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
";


$(document).ready(function() {

	if (getFlashVersion() !== false) {
		$('#inputFiledata<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
').uploadify({ 
			'uploader': webroot + 'swf/uploadify.swf',
			'script':    multiUploadUrl,
			multi: true,
			auto: true,
			'cancelImg': webroot + 'img/uploadCancel.png',
			'buttonImg': webroot + 'img/multiupload-browse.png',
			width: 124,
			wmode:"transparent",
			buttonText : 'browssssse',
			displayData: 'percentage',
			onComplete: completeUpload<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
,
			scriptData: { userid: u_id}
		});

		$("#flashUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
 a").click(function() {
			$("#ajaxUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
").show();
			$("#flashUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
").hide();
		});

		$("#ajaxUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
 a").click(function() {
			$("#ajaxUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
").hide();
			$("#flashUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
").show();
		});
		
	} else {
		$("#flashUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
").hide();
		$("#ajaxUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
").show();
		$("#ajaxUploadContainer<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
 a").hide();
	}

	
});

function completeUpload<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
(event, queueID, fileObj,response) {
	if (isNaN(parseInt(response))) { 
		$("#inputFiledata<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
" + queueID + " .fileName").text(" Error - " + fileObj.name + " - " + response);
		$("#inputFiledata<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
" + queueID).css({ 'border': '3px solid #FBCBBC', 'background-color': '#FDE5DD'});
		return false;
	} else {
		objids = new Array();
		objids[0] = response;
		$("#loading<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
").show();
		commitUploadItem<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
(objids);
		return true;
	}
}

//-->
</script>

<div id="flashUploadContainer<?php echo (($tmp = @$_smarty_tpl->tpl_vars['uploadIdSuffix']->value)===null||$tmp==='' ? '' : $tmp);?>
" style="padding:20px 0px 0px 0px">
<input type="file" name="Filedata<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
" id="inputFiledata<?php echo $_smarty_tpl->tpl_vars['uploadIdSuffix']->value;?>
" />
<p><a href="javascript:void(0);"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
If you have any problems try with browser upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></p>
</div>

<?php echo smarty_function_assign_associative(array('var'=>"params",'uploadIdSuffix'=>$_smarty_tpl->tpl_vars['uploadIdSuffix']->value),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_upload_ajax',$_smarty_tpl->tpl_vars['params']->value);?>
<?php }} ?>