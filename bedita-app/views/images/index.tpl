<!-- inizio blocco upload -->

{$html->css('swfupload')}
{$javascript->link("swfupload/SWFUpload")}
{$javascript->link("swfupload/callbacks")}
<script type="text/javascript">
{literal}
<!--

function showHideBox(boxId,showIt) {
	document.getElementById(boxId).style.display=(showIt) ? '' : 'none';
	document.getElementById(boxId+'Th').className=(showIt) ? 'boxSelected' : 'boxNotSelected';
}

function localShowHideBox(boxToShowId) {
	showHideBox('uploadBox',false);showHideBox('searchBox',false);showHideBox('linkBox',false);
	showHideBox(boxToShowId,true);
}

function uploadProgress(file, bytesLoaded) {
	var progress = document.getElementById(file.id + "progress");
	var percent = Math.ceil((bytesLoaded / file.size) * 200);
	progress.style.background = "#f0f0f0 url({/literal}{$session->webroot}{literal}img/swfupload/progressbar.png) no-repeat -" + (200 - percent) + "px 0";
}

var swfu;

window.onload = function() {

	swfu = new SWFUpload({
		upload_script : "{/literal}{$html->url('/files/upload')}{literal}",
		target : "SWFUploadTarget",
		flash_path : "{/literal}{$session->webroot}{literal}js/swfupload/SWFUpload.swf",
		browse_link_innerhtml : "Browse",
		upload_link_innerhtml : "Upload queue",
		browse_link_class : "swfuploadbtn browsebtn",
		upload_link_class : "swfuploadbtn uploadbtn",
		flash_loaded_callback : 'swfu.flashLoaded',
		upload_file_queued_callback : "fileQueued",
		upload_file_start_callback : 'uploadFileStart',
		upload_progress_callback : 'uploadProgress',
		upload_file_complete_callback : 'uploadFileComplete',
		upload_file_cancel_callback : 'uploadFileCancelled',
		upload_queue_complete_callback : 'uploadQueueComplete',
		upload_error_callback : 'uploadError',
		upload_cancel_callback : 'uploadCancel',
		auto_upload : false
	});

};

//-->
{/literal}
</script>

<style type="text/css">
{literal}
th.boxSelected {border:solid #000 1px;background-color:#FFF; height: 20px;}
th.boxNotSelected {border:solid #000 1px;background-color:#DDD; height: 20px;}
{/literal}
</style>

</head>
<body>

<table cellpadding="0" cellspacing="0" style="width:100%">
<thead>
<tr>
	<th id="uploadBoxTh" class="boxSelected"><a href="#" onclick="javascript:localShowHideBox('uploadBox');">{t}Upload{/t}</a></th>
	<th id="searchBoxTh" class="boxNotSelected"><a href="#" onclick="javascript:localShowHideBox('searchBox');">{t}Search{/t}</a></th>
	<th id="linkBoxTh" class="boxNotSelected"><a href="#" onclick="javascript:localShowHideBox('linkBox');">{t}Link{/t}</a></th></tr>
</thead>
<tbody>
<tr>
	<td colspan="3">
		<div id="uploadBox" style="padding:5px 5px 5px 5px">
			<div id="wrapper">
				<div id="content">
					<div id="SWFUploadTarget">
						<form action="{$html->url('/files/upload')}" method="post" enctype="multipart/form-data">
							{$form->file("Filedata")}
							<input type="submit" value="{t}Upload{/t}" />
						</form>
					</div>
					<h4 id="queueinfo">Queue is empty</h4>
					<div id="SWFUploadFileListingFiles"></div>
					<br class="clr" />
					<a class="swfuploadbtn" id="cancelqueuebtn" href="javascript:cancelQueue();">Cancel queue</a>
				</div>
			</div>
		</div>
		<div id="searchBox" style="display:none;padding:5px 5px 5px 5px">{t}Search{/t} ...
			<input type="text" name="data[searchkey]"/><input type="submit" value="{t}Search{/t}"/>
		</div>
		<div id="linkBox" style="display:none;padding:5px 5px 5px 5px">Link ...
			<input type="text" name="data[imageurl]"/><input type="submit" value="{t}Add{/t}"/>
		</div>
	</td>
</tr>
</tbody>
</table>
<!-- fine blocco upload -->