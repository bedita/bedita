<!-- start upload block-->
{$html->css('swfupload')}
{$html->script("swfupload/SWFUpload-src")}
{$html->script("swfupload/callbacks")}

<script type="text/javascript">
<!--

var title_dialog	= '{t}files queued{/t}' ;
var postappend_info	= '{t}files queued{/t}' ;
var URLDelete		= '{$html->url('/files/deleteFile')}' ;


var swfu;			// upload object
var files = {} ;	// file queue

function commitFileUpload(tmp) {
	{$relation}CommitUploadItem(tmp, '{$relation}') ;
}

function rollbackFileUpload() {
	{$relation}RollbackUploadItem() ;
}

function createThumbnails() {
	var tmp = new Array() ;
	$(".uploadCompleted").each(function() {
		// Get filename
		var item = this ;
		var id = $(this).prop("id") ; 
		try {
			if(files[id].length) {
				tmp[tmp.length] = files[id] ;
			}
		} catch(e) {
			alert("Error on id " + id);
			alert(e);
		}
	}) ;
	commitFileUpload(tmp) ;
}

// close modal window, reset operations (cancel)
var counter = 0 ;
function closeEsc() {
	// If no upload, it exists without messages
	if($(".uploadCompleted").size()) {
		if(!confirm("Are you sure that you want to continue?")) return ;	
	}
	$(".uploadCompleted").each(function() {
		// Get filename
		var item = this ;
		var id = $(this).prop("id") ; 
		try {
			if(files[id].length) {
				var fileName= files[id] ;
				// Delete file
				counter++ ;
				jQuery.post( URLDelete, { 'filename': fileName }, function (data, textStatus) {
					$(item).remove() ;
					files[id] = '' ;
					counter-- ;
				}) ;
			}
		} catch(e) {
			alert("Error on id " + id);
			alert(e);
		}
	}) ;
	rollbackFileUpload() ;
}

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
	progress.style.background = "#f0f0f0 url({$session->webroot}img/swfupload/progressbar.png) no-repeat -" + (200 - percent) + "px 0";
}

function errorsFunction(errcode, file, msg) {
	alert(errcode + ", " + file.name + ", " + msg);
}

function fileCompletedFunction(file) {
	alert(file.name + " completed");
}

window.onload = function() {
	swfu = new SWFUpload({
		upload_script : "{$html->url('/files/upload')}",
		target : "SWFUploadTarget",
		flash_path : "{$session->webroot}js/swfupload/SWFUpload.swf",
		browse_link_innerhtml : "{t}Browse{/t}",
		upload_link_innerhtml : "{t}Upload queue{/t}",
		browse_link_class : "swfuploadbtn browsebtn",
		upload_link_class : "swfuploadbtn uploadbtn",
		flash_loaded_callback : 'swfu.flashLoaded',
		upload_file_queued_callback : "befileQueued",
		upload_file_start_callback : 'beuploadFileStart',
		upload_progress_callback : 'uploadProgress',
		upload_file_complete_callback : 'beuploadFileComplete',
		upload_file_cancel_callback : 'beuploadFileCancelled',
		upload_queue_complete_callback : 'beUploadQueueComplete',
		upload_file_error_callback : 'beuploadError',
		upload_cancel_callback : 'uploadCancel',
		auto_upload : false,
		allowed_filesize: 13000
	});
};

// Append file in the queue
function befileQueued(file, queuelength) {
	if(!$("#SWFUploadFileListingFiles ul").size()) {
		$("#SWFUploadFileListingFiles").append("<ul><\/ul>")  ;
	}
	// New item text
	var addFileItemListHtml = "<li id='"+file.id+"' class='SWFUploadFileItem'>"+file.name +" <span class='progressBar' id='" + file.id + "progress'><\/span><a id='" + file.id + "deletebtn' class='cancelbtn' href='javascript:swfu.cancelFile(\"" + file.id + "\");'><!-- IE --><\/a><\/li>" ;
	// Append new item
	$("#SWFUploadFileListingFiles ul").append(addFileItemListHtml) ;
	// Update info in the queue
	$("#queueinfo").html(queuelength + " " + postappend_info) ;
	$("#" + swfu.movieName + "UploadBtn").show() ;
	$("#cancelqueuebtn").show() ;
}

// Remove file from queue
function beuploadFileCancelled(file, queuelength) {
	$("#"+file.id+"").remove() ;
	$("#queueinfo").html(queuelength + " "+ postappend_info) ;
}

// start upload
function beuploadFileStart(file, position, queuelength) {
	$("#"+file.id+"").prop("class", $("#" + file.id + "").prop("class") + " fileUploading") ;
	$("#queueinfo").html("{t}Uploading file{/t} " + position + "/" + queuelength) ;
}

function beuploadFileComplete(file) {
	files[file.id] = file.name ;
	$("#"+file.id+"").prop("class", "SWFUploadFileItem uploadCompleted") ;
}

function beuploadError(errcode, file, msg) {
	switch(errcode) {
		case -10: beSetupErrorFile(file, msg) ; break;
		case -20:	// No upload script specified
			alert("Error Code: No upload script, File name: " + file.name + ", Message: " + msg);
			break;
		case -30:	// IOError
			alert("Error Code: IO Error, File name: " + file.name + ", Message: " + msg);
			break;
		case -40:	// Security error
			alert("Error Code: Security Error, File name: " + file.name + ", Message: " + msg);
			break;
		case -50:	// Filesize too big
			alert("Error Code: Filesize exceeds limit, File name: " + file.name + ", File size: " + file.size + ", Message: " + msg);
			break;
	}
}

function beSetupErrorFile(file, code) {
	// Define messagge
	switch(code) {
		case 501: msg = "UPLOAD_ERR_INI_SIZE" ; break ;
		case 502: msg = "UPLOAD_ERR_FORM_SIZE" ; break ;
		case 503: msg = "UPLOAD_ERR_PARTIAL" ; break ;
		case 504: msg = "UPLOAD_ERR_NO_FILE" ; break ;
		case 506: msg = "UPLOAD_ERR_NO_TMP_DIR" ; break ;
		case 507: msg = "UPLOAD_ERR_CANT_WRITE" ; break ;
		case 508: msg = "UPLOAD_ERR_EXTENSION" ; break ;
		case 530: msg = "BEDITA_FILE_EXIST" ; break ;
		case 531: msg = "BEDITA_MIME" ; break ;
		case 532: msg = "BEDITA_SAVE_STREAM" ; break ;
		case 533: msg = "BEDITA_DELETE_STREAM" ; break ;
	}
	// write message
	$("#" + file.id).append("<span>" + msg + "<\/span>").prop("class", $("#" + file.id).prop("class") + " uploadError") ;
}

/**
 * Operation done message.
 * If no file ok, it doesn't show ok button
 */
function beUploadQueueComplete(file) {
	// if at least one file uploaded, show "ok" button
	var ok = 0 ;
	$("#SWFUploadFileListingFiles li").each(function() {
		var className = $(this).prop("class") ; 
		if(!className.match(/uploadError/)) ok++ ;
	}) ;
	
	if(ok>0) {
		$("#queueinfo").html("{t}Upload end{/t}") ;
		createThumbnails();
	} else {
		$("#queueinfo").html("{t}Errors during upload{/t}") ;
	}
}

// Delete queue
function beCancelQueue() {
	swfu.cancelQueue();
	$(swfu.movieName + "UploadBtn").hide() ;
	$("cancelqueuebtn").hide() ;
}
 
//-->

</script>


		<div id="uploadBox" style="border:5px solid red; padding:5px;">
			<div id="wrapper">
				<div id="content">
						
					<p id="queueinfo">{t}Queue is empty{/t}</p>
					
					<div style="border:5px solid gold" id="SWFUploadFileListingFiles"></div>
					
					<br class="clr" />
					
					<div id="SWFUploadTarget" style="border:5px solid green;">
						<form id="uploadForm" 
						action="{$html->url('/files/upload')}" method="post" enctype="multipart/form-data">
							<input type="file" name="Filedata" />
							<input type="submit" value="{t}Upload{/t}"/>
						</form>
					</div>	
					
					<input type="button" onclick="javascript:beCancelQueue();" 
					value="{t}Cancel queue{/t}"/>
				
				</div>
			</div>
		</div>

<!-- end upload block -->