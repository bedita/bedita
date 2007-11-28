<!-- inizio blocco upload -->
<div>
{$html->css('swfupload')}
{$javascript->link("swfupload/SWFUpload-src")}
{$javascript->link("swfupload/callbacks")}
<script type="text/javascript">
<!--

var title_dialog	= '{t}files queued{/t}' ;
var postappend_info	= '{t}files queued{/t}' ;

var URLDelete		= '{$html->url('/files/deleteFile')}' ;

{literal}

var swfu;			// oggetto gestione upload
var files = {} ;	// file in coda


// funzione per la chiusura della finestra modale confermando le operazioni
function closeOK() {
	var tmp = new Array() ;
	
	$(".uploadCompleted").each(function() {
		// Preleva il nome del file
		var item = this ;
		var id = $(this).attr("id") ; 
		try {
			if(files[id].length) {
				tmp[tmp.length] = files[id] ;
				
			}
		} catch(e) {
			
		}
		
	}) ;
	
	try {
		commitUploadImage(tmp) ;
	} catch(e) {
		parent.commitUploadImage(tmp) ;
	}
}

// funzione per la chiusura della finestra modale annullando le operazioni le operazioni
var counter = 0 ;
function closeEsc() {
	// Se non sono stati fatti upload esce senza avvisi
	if($(".uploadCompleted").size()) {
		if(!confirm("Sicuro di voler continuare?")) return ;	
	}

	$(".uploadCompleted").each(function() {
		// Preleva il nome del file
		var item = this ;
		var id = $(this).attr("id") ; 
		try {
			if(files[id].length) {
				var fileName= files[id] ;
				
				// Richiede la cancellazione del file
				counter++ ;
				jQuery.post( URLDelete, {'filename': fileName}, function (data, textStatus) {
					$(item).remove() ;
					files[id] = '' ;
					
					counter-- ;
				}) ;
			}
		} catch(e) {
			
		}
		
	}) ;
	
	try {
		rollbackUploadImage() ;
	} catch(e) {
		parent.rollbackUploadImage() ;
	}
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
	progress.style.background = "#f0f0f0 url({/literal}{$session->webroot}{literal}img/swfupload/progressbar.png) no-repeat -" + (200 - percent) + "px 0";
}

function errorsFunction(errcode, file, msg) {
	alert(errcode + ", " + file.name + ", " + msg);
}


function fileCompletedFunction(file) {
	alert(file.name + " completed");
}


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
		upload_file_queued_callback : "befileQueued",
		upload_file_start_callback : 'beuploadFileStart',
		upload_progress_callback : 'uploadProgress',
		upload_file_complete_callback : 'beuploadFileComplete',
		upload_file_cancel_callback : 'beuploadFileCancelled',
		upload_queue_complete_callback : 'beUploadQueueComplete',
		upload_file_error_callback : 'beuploadError',
		upload_cancel_callback : 'uploadCancel',
		auto_upload : false
	});

};

// Inserimento di un file in lista
function befileQueued(file, queuelength) {
	// Se non c'e', crea la lista vuota 
	if(!$("#SWFUploadFileListingFiles ul").size()) {
		$("#SWFUploadFileListingFiles").append("<h4>"+title_dialog+"</h4>").append("<ul></ul>")  ;
	}
	
	// Testo nuovo elemento
	var addFileItemListHtml = "						\
		<li id='"+file.id+"' class='SWFUploadFileItem'>								\
			"+file.name +" <span class='progressBar' id='" + file.id + "progress'></span>	\
			<a id='" + file.id + "deletebtn' class='cancelbtn' href='javascript:swfu.cancelFile(\"" + file.id + "\");'><!-- IE --></a> \
		</li>	\
	" ;
	
	// Inserisce l'elemento
	$("#SWFUploadFileListingFiles ul").append(addFileItemListHtml) ;
	
	// Aggiorna le info sulla lista
	$("#queueinfo").html(queuelength + " " + postappend_info) ;
	
	$("#" + swfu.movieName + "UploadBtn").show() ;
	$("#cancelqueuebtn").show() ;
}

// Elimina un file dalla lista
function beuploadFileCancelled(file, queuelength) {
	$("#"+file.id+"").remove() ;
	$("#queueinfo").html(queuelength + " "+ postappend_info) ;
}

// inizio upload
function beuploadFileStart(file, position, queuelength) {
	$("#"+file.id+"").attr("class", $("#"+file.id+"").attr("class") + " fileUploading") ;
	$("#queueinfo").html("Uploading file " + position + " of " + queuelength) ;
}

function beuploadFileComplete(file) {
	files[file.id] = file.name ;
	$("#"+file.id+"").attr("class", "SWFUploadFileItem uploadCompleted") ;
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
	// Definisce il messaggio
	switch(code) {
		case 501: msg = "UPLOAD_ERR_INI_SIZE" ; break ;
		case 502: msg = "UPLOAD_ERR_FORM_SIZE" ; break ;
		case 503: msg = "UPLOAD_ERR_PARTIAL" ; break ;
		case 504: msg = "UPLOAD_ERR_NO_FILE" ; break ;
		case 506: msg = "UPLOAD_ERR_NO_TMP_DIR" ; break ;
		case 507: msg = "UPLOAD_ERR_CANT_WRITE" ; break ;
		case 508: msg = "UPLOAD_ERR_EXTENSION" ; break ;
		
		case 530: msg = "BEDITA_FILE_EXISIST" ; break ;
		case 531: msg = "BEDITA_MIME" ; break ;
		case 532: msg = "BEDITA_SAVE_STREAM" ; break ;
		case 533: msg = "BEDITA_DELETE_STREAM" ; break ;
	}

	// Scrive il messaggio
	$("#"+file.id).append("<span>"+msg+"</span>").attr("class", $("#"+file.id).attr("class") + " uploadError") ;
}

/**
 Messaggio di fine operazione.
 SE nessun file ok, non visualizza il comando ok
**/
function beUploadQueueComplete(file) {
	// Se c'e' almeno un file uploadato, visualizza il comando "ok", rende definitiva l'operazione
	var ok = 0 ;
	$("#SWFUploadFileListingFiles li").each(function() {
		var className = $(this).attr("class") ; 
		if(!className.match(/uploadError/)) ok++ ;
	}) ;
	if(ok) {
		$("#okqueuebtn").show() ;
		$("#annullaqueuebtn").show() ;
	}
	
	// Fine operazioni
	$("#queueinfo").html("Upload end ") ;
}

// Cancella lista
function beCancelQueue() {
	swfu.cancelQueue();
	$(swfu.movieName + "UploadBtn").hide() ;
	$("cancelqueuebtn").hide() ;
}


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
<tbody>
<tr>
	<td colspan="3">
		<div id="uploadBox" style="padding:5px 5px 5px 5px">
			<div id="wrapper">
				<div id="content">
					<div id="SWFUploadTarget">
						<form id="test" action="{$html->url('/files/upload')}" method="post" enctype="multipart/form-data">
							{$form->file("Filedata")}
							<input type="hidden" name="MAX_FILE_SIZE" value="3000" />
							<input type="submit" value="{t}Upload{/t}" />
						</form>
					</div>
					<h4 id="queueinfo">Queue is empty</h4>
					<div id="SWFUploadFileListingFiles"></div>
					<br class="clr" />
					<a class="swfuploadbtn" id="cancelqueuebtn" href="javascript:beCancelQueue();">Cancel queue</a>
					<a class="swfuploadbtn" id="okqueuebtn" href="javascript:closeOK();" style="display:block">Ok</a>
					<a class="swfuploadbtn" id="annullaqueuebtn" href="javascript:closeEsc();" style="display:block">Annulla</a>					
				</div>
			</div>
		</div>
	</td>
</tr>
</tbody>
</table>
</div>
<!-- fine blocco upload -->