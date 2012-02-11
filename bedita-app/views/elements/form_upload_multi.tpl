{$html->script('swfobject', false)}
{$html->script('jquery/jquery.uploadify.min', false)}
{if empty($uploadIdSuffix)}
	{assign var=uploadIdSuffix value=""}
{/if}

<script type="text/javascript">
<!--
var webroot = "{$html->webroot}";
var multiUploadUrl = "{$html->url('/')}files/upload";
var u_id = "{$session->read("BEAuthUser.id")}";
var uploadIdSuffix = "{$uploadIdSuffix}";


$(document).ready(function() {

	if (getFlashVersion() !== false) {
		$('#inputFiledata{$uploadIdSuffix}').uploadify({ 
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
			onComplete: completeUpload{$uploadIdSuffix},
			scriptData: { userid: u_id}
		});

		$("#flashUploadContainer{$uploadIdSuffix} a").click(function() {
			$("#ajaxUploadContainer{$uploadIdSuffix}").show();
			$("#flashUploadContainer{$uploadIdSuffix}").hide();
		});

		$("#ajaxUploadContainer{$uploadIdSuffix} a").click(function() {
			$("#ajaxUploadContainer{$uploadIdSuffix}").hide();
			$("#flashUploadContainer{$uploadIdSuffix}").show();
		});
		
	} else {
		$("#flashUploadContainer{$uploadIdSuffix}").hide();
		$("#ajaxUploadContainer{$uploadIdSuffix}").show();
		$("#ajaxUploadContainer{$uploadIdSuffix} a").hide();
	}

	
});

function completeUpload{$uploadIdSuffix}(event, queueID, fileObj,response) {
	if (isNaN(parseInt(response))) { 
		$("#inputFiledata{$uploadIdSuffix}" + queueID + " .fileName").text(" Error - " + fileObj.name + " - " + response);
		$("#inputFiledata{$uploadIdSuffix}" + queueID).css({ 'border': '3px solid #FBCBBC', 'background-color': '#FDE5DD'});
		return false;
	} else {
		objids = new Array();
		objids[0] = response;
		$("#loading{$uploadIdSuffix}").show();
		commitUploadItem{$uploadIdSuffix}(objids);
		return true;
	}
}

//-->
</script>

<div id="flashUploadContainer{$uploadIdSuffix|default:''}" style="padding:20px 0px 0px 0px">
<input type="file" name="Filedata{$uploadIdSuffix}" id="inputFiledata{$uploadIdSuffix}" />
<p><a href="javascript:void(0);">{t}If you have any problems try with browser upload{/t}</a></p>
</div>

{assign_associative var="params" uploadIdSuffix=$uploadIdSuffix}
{$view->element('form_upload_ajax', $params)}