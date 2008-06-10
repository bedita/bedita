<script type="text/javascript">
var urlGetObj		= '{$html->url("/streams/get_item_form_by_id")}' ;
var containerItem = "#multimediaItems";

{literal}
function commitUploadItem(IDs, rel) {
	//$("#loading").show();
	var emptyDiv = "<div  class=\"multimediaitem itemBox\"><\/div>";
	for(var i=0 ; i < IDs.length ; i++) {
		var id = escape(IDs[i]) ;
		$(emptyDiv).load(urlGetObj, {'id': id, 'relation':rel}, function (responseText, textStatus, XMLHttpRequest) {
			$(containerItem).append(this).reorderListItem() ; 
			//$("#loading").hide();
		}) ;
	}	
}


function showResponse(data) {
	$("#loading").hide();
	if (data.UploadErrorMsg) {
    	$("#msgUpload").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
    } else {
	    var tmp = new Array() ;
	    var countFile = 0; 
	    $.each(data, function(entryIndex, entry) {
	    	tmp[countFile++] = entry['fileId'];
	    });
		commitUploadItem(tmp, "attach");
	}
}

function resetError() {
	$("#msgUpload").empty();
	$("#loading").show();
}

$(document).ready(function() {  
	var optionsForm = {
		beforeSubmit:	resetError,
        success:    	showResponse,  // post-submit callback 
        url:       		"{/literal}{$html->url('/files/uploadAjax')}{literal}",       // override form's 'action' attribute 
        dataType:  		'json'        // 'xml', 'script', or 'json' (expected server response type) 
    }; 

    $("#uploadForm").bind("click", function() {
    	$('#updateForm').ajaxSubmit(optionsForm);
    	return false;
    }); 
	 
});
{/literal}
</script>
<div>
	
	<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td colspan="2">
			<strong>{t}file{/t}:</strong>
			<input type="file" name="Filedata" />
			<input type="button" id="uploadForm" value="{t}Upload{/t}"/>
			</td>
		</tr>
		
		<tr>
			<td><strong>{t}Title{/t}</strong></td>
			<td><strong>{t}Description{/t}</strong></td>
		</tr>
		<tr>
			<td><input type="text" name="streamUploaded[title]" class="formtitolo" value=""></td>
			<td>
				<textarea name="streamUploaded[description]" style="width:280px; height:90px;"></textarea>
			</td>
		</tr>
	</table>
	
<div id="msgUpload"></div>
</div>