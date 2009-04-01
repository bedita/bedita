
{$javascript->link('jquery/jquery.uploadify', false)}

<script type="text/javascript">
<!--
var webroot = "{$html->webroot}";
var multiUploadUrl = "{$html->url('/')}files/upload";
var u_id = "{$session->read("BEAuthUser.id")}";
{literal}
$(document).ready(function() {
	$('#inputFiledata').fileUpload({
		'uploader': webroot + 'swf/uploader.swf',
		'script':    multiUploadUrl,
		multi: true,
		auto: true,
		'cancelImg': webroot + 'img/uploadCancel.png',
		'buttonImg': webroot + 'img/multiupload-browse.png',
		width: 124,
		buttonText : 'browssssse',
		displayData: 'percentage',
		onComplete: completeUpload,
		scriptData: {userid: u_id}
	});
});

function completeUpload(event, queueID, fileObj,response) {
	if (isNaN(parseInt(response))) {
		$("#inputFiledata" + queueID + " .fileName").text(" Error - " + fileObj.name + " - " + response);
		$("#inputFiledata" + queueID).css({'border': '3px solid #FBCBBC', 'background-color': '#FDE5DD'});
		return false;
	} else {
		objids = new Array();
		objids[0] = response;
		$("#loading").show();
		commitUploadItem(objids, "attach");
		return true;
	}
}

{/literal}
//-->
</script>

<div style="padding:20px 0px 0px 20px">
<input type="file" name="Filedata" id="inputFiledata" />
</div>