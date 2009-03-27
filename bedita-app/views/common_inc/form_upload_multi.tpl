
{$javascript->link('jquery/jquery.uploadify', false)}

<script type="text/javascript">
<!--
var webroot = "{$html->webroot}";
var multiUploadUrl = "{$html->url('/')}files/upload";
{literal}
$(document).ready(function() {
	$('#inputFiledata').fileUpload({
		'uploader': webroot + 'swf/uploader.swf',
		'script':    multiUploadUrl,
		multi: true,
		auto: true,
		'cancelImg': webroot + 'img/uploadCancel.png',
		displayData: 'percentage',
		onComplete: completeUpload
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
		commitUploadItem(objids, "attach");
		return true;
	}
}

{/literal}
//-->
</script>


<input type="file" name="Filedata" id="inputFiledata" />